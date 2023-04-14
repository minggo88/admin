var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __read = (this && this.__read) || function (o, n) {
    var m = typeof Symbol === "function" && o[Symbol.iterator];
    if (!m) return o;
    var i = m.call(o), r, ar = [], e;
    try {
        while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
    }
    catch (error) { e = { error: error }; }
    finally {
        try {
            if (r && !r.done && (m = i.return)) m.call(i);
        }
        finally { if (e) throw e.error; }
    }
    return ar;
};
var __spread = (this && this.__spread) || function () {
    for (var ar = [], i = 0; i < arguments.length; i++) ar = ar.concat(__read(arguments[i]));
    return ar;
};
var __values = (this && this.__values) || function (o) {
    var m = typeof Symbol === "function" && o[Symbol.iterator], i = 0;
    if (m) return m.call(o);
    return {
        next: function () {
            if (o && i >= o.length) o = void 0;
            return { value: o && o[i++], done: !o };
        }
    };
};
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator.throw(value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : new P(function (resolve) { resolve(result.value); }).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = y[op[0] & 2 ? "return" : op[0] ? "throw" : "next"]) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [0, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var std;
(function (std) {
    function for_each(first, last, fn) {
        for (var it = first; !it.equals(last); it = it.next())
            fn(it.value);
        return fn;
    }
    std.for_each = for_each;
    function for_each_n(first, n, fn) {
        for (var i = 0; i < n; ++i) {
            fn(first.value);
            first = first.next();
        }
        return first;
    }
    std.for_each_n = for_each_n;
    function all_of(first, last, pred) {
        for (var it = first; !it.equals(last); it = it.next())
            if (pred(it.value) == false)
                return false;
        return true;
    }
    std.all_of = all_of;
    function any_of(first, last, pred) {
        for (var it = first; !it.equals(last); it = it.next())
            if (pred(it.value) == true)
                return true;
        return false;
    }
    std.any_of = any_of;
    function none_of(first, last, pred) {
        return !any_of(first, last, pred);
    }
    std.none_of = none_of;
    function equal(first1, last1, first2, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        while (!first1.equals(last1))
            if (!pred(first1.value, first2.value))
                return false;
            else {
                first1 = first1.next();
                first2 = first2.next();
            }
        return true;
    }
    std.equal = equal;
    function lexicographical_compare(first1, last1, first2, last2, comp) {
        if (comp === void 0) { comp = std.less; }
        while (!first1.equals(last1))
            if (first2.equals(last2) || comp(first2.value, first1.value))
                return false;
            else if (comp(first1.value, first2.value))
                return true;
            else {
                first1 = first1.next();
                first2 = first2.next();
            }
        return !first2.equals(last2);
    }
    std.lexicographical_compare = lexicographical_compare;
    function find(first, last, val) {
        for (var it = first; !it.equals(last); it = it.next())
            if (std.equal_to(it.value, val))
                return it;
        return last;
    }
    std.find = find;
    function find_if(first, last, pred) {
        for (var it = first; !it.equals(last); it = it.next())
            if (pred(it.value))
                return it;
        return last;
    }
    std.find_if = find_if;
    function find_if_not(first, last, pred) {
        for (var it = first; !it.equals(last); it = it.next())
            if (pred(it.value) == false)
                return it;
        return last;
    }
    std.find_if_not = find_if_not;
    function find_end(first1, last1, first2, last2, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        if (first2.equals(last2))
            return last1;
        var ret = last1;
        for (; !first1.equals(last1); first1 = first1.next()) {
            var it1 = first1;
            var it2 = first2;
            while (pred(it1.value, it2.value)) {
                it1 = it1.next();
                it2 = it2.next();
                if (it2.equals(last2)) {
                    ret = first1;
                    break;
                }
                else if (it1.equals(last1))
                    return ret;
            }
        }
        return ret;
    }
    std.find_end = find_end;
    function find_first_of(first1, last1, first2, last2, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        for (; !first1.equals(last1); first1 = first1.next())
            for (var it = first2; !it.equals(last2); it = it.next())
                if (pred(it.value, first1.value))
                    return first1;
        return last1;
    }
    std.find_first_of = find_first_of;
    function adjacent_find(first, last, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        if (!first.equals(last)) {
            var next_1 = first.next();
            while (!next_1.equals(last)) {
                if (pred(first.value, last.value))
                    return first;
                first = first.next();
                next_1 = next_1.next();
            }
        }
        return last;
    }
    std.adjacent_find = adjacent_find;
    function search(first1, last1, first2, last2, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        if (first2.equals(last2))
            return first1;
        for (; !first1.equals(last1); first1 = first1.next()) {
            var it1 = first1;
            var it2 = first2;
            while (pred(it1.value, it2.value)) {
                it1 = it1.next();
                it2 = it2.next();
                if (it2.equals(last2))
                    return first1;
                else if (it1.equals(last1))
                    return last1;
            }
        }
        return last1;
    }
    std.search = search;
    function search_n(first, last, count, val, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        var limit = first.advance(std.distance(first, last) - count);
        for (; !first.equals(limit); first = first.next()) {
            var it = first;
            var i = 0;
            while (pred(it.value, val)) {
                it = it.next();
                if (++i == count)
                    return first;
            }
        }
        return last;
    }
    std.search_n = search_n;
    function mismatch(first1, last1, first2, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        while (!first1.equals(last1) && pred(first1.value, first2.value)) {
            first1 = first1.next();
            first2 = first2.next();
        }
        return std.make_pair(first1, first2);
    }
    std.mismatch = mismatch;
    function count(first, last, val) {
        var cnt = 0;
        for (var it = first; !it.equals(last); it = it.next())
            if (std.equal_to(it.value, val))
                cnt++;
        return cnt;
    }
    std.count = count;
    function count_if(first, last, pred) {
        var cnt = 0;
        for (var it = first; !it.equals(last); it = it.next())
            if (pred(it.value))
                cnt++;
        return cnt;
    }
    std.count_if = count_if;
})(std || (std = {}));
var std;
(function (std) {
    function copy(first, last, result) {
        for (; !first.equals(last); first = first.next()) {
            result.value = first.value;
            result = result.next();
        }
        return result;
    }
    std.copy = copy;
    function copy_n(first, n, result) {
        for (var i = 0; i < n; i++) {
            result.value = first.value;
            first = first.next();
            result = result.next();
        }
        return result;
    }
    std.copy_n = copy_n;
    function copy_if(first, last, result, pred) {
        for (; !first.equals(last); first = first.next()) {
            if (!pred(first.value))
                continue;
            result.value = first.value;
            result = result.next();
        }
        return result;
    }
    std.copy_if = copy_if;
    function copy_backward(first, last, result) {
        last = last.prev();
        for (; !last.equals(first); last = last.prev()) {
            result.value = last.value;
            result = result.prev();
        }
        return result;
    }
    std.copy_backward = copy_backward;
    function fill(first, last, val) {
        for (; !first.equals(last); first = first.next())
            first.value = val;
    }
    std.fill = fill;
    function fill_n(first, n, val) {
        for (var i = 0; i < n; i++) {
            first.value = val;
            first = first.next();
        }
        return first;
    }
    std.fill_n = fill_n;
    function transform() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        if (args.length == 4)
            return unary_transform.apply(null, args);
        else
            return binary_transform.apply(null, args);
    }
    std.transform = transform;
    function unary_transform(first, last, result, op) {
        for (; !first.equals(last); first = first.next()) {
            result.value = op(first.value);
            result = result.next();
        }
        return result;
    }
    function binary_transform(first1, last1, first2, result, binary_op) {
        while (!first1.equals(last1)) {
            result.value = binary_op(first1.value, first2.value);
            first1 = first1.next();
            first2 = first2.next();
            result = result.next();
        }
        return result;
    }
    function generate(first, last, gen) {
        for (; !first.equals(last); first = first.next())
            first.value = gen();
    }
    std.generate = generate;
    function generate_n(first, n, gen) {
        for (var i = 0; i < n; i++) {
            first.value = gen();
            first = first.next();
        }
        return first;
    }
    std.generate_n = generate_n;
    function unique(first, last, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        var ret = first;
        for (var it = first.next(); !it.equals(last);) {
            if (pred(it.value, it.prev().value) == true)
                it = it.source().erase(it);
            else {
                ret = it;
                it = it.next();
            }
        }
        return ret;
    }
    std.unique = unique;
    function unique_copy(first, last, result, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        if (first.equals(last))
            return result;
        result.value = first.value;
        first = first.next();
        for (; !first.equals(last); first = first.next())
            if (!pred(first.value, result.value)) {
                result = result.next();
                result.value = first.value;
            }
        return result;
    }
    std.unique_copy = unique_copy;
    function remove(first, last, val) {
        var ret = last;
        for (var it = first; !it.equals(last);) {
            if (std.equal_to(it.value, val) == true)
                it = it.source().erase(it);
            else {
                ret = it;
                it = it.next();
            }
        }
        return ret;
    }
    std.remove = remove;
    function remove_if(first, last, pred) {
        var ret = last;
        for (var it = first; !it.equals(last);) {
            if (pred(it.value) == true)
                it = it.source().erase(it);
            else {
                ret = it;
                it = it.next();
            }
        }
        return ret;
    }
    std.remove_if = remove_if;
    function remove_copy(first, last, result, val) {
        for (; !first.equals(last); first = first.next()) {
            if (std.equal_to(first.value, val))
                continue;
            result.value = first.value;
            result = result.next();
        }
        return result;
    }
    std.remove_copy = remove_copy;
    function remove_copy_if(first, last, result, pred) {
        for (; !first.equals(last); first = first.next()) {
            if (pred(first.value))
                continue;
            result.value = first.value;
            result = result.next();
        }
        return result;
    }
    std.remove_copy_if = remove_copy_if;
    function replace(first, last, old_val, new_val) {
        for (var it = first; !it.equals(last); it = it.next())
            if (std.equal_to(it.value, old_val))
                it.value = new_val;
    }
    std.replace = replace;
    function replace_if(first, last, pred, new_val) {
        for (var it = first; !it.equals(last); it = it.next())
            if (pred(it.value) == true)
                it.value = new_val;
    }
    std.replace_if = replace_if;
    function replace_copy(first, last, result, old_val, new_val) {
        for (; !first.equals(last); first = first.next()) {
            if (std.equal_to(first.value, old_val))
                result.value = new_val;
            else
                result.value = first.value;
            result = result.next();
        }
        return result;
    }
    std.replace_copy = replace_copy;
    function replace_copy_if(first, last, result, pred, new_val) {
        for (; !first.equals(last); first = first.next()) {
            if (pred(first.value))
                result.value = new_val;
            else
                result.value = first.value;
            result = result.next();
        }
        return result;
    }
    std.replace_copy_if = replace_copy_if;
    function iter_swap(x, y) {
        x.swap(y);
    }
    std.iter_swap = iter_swap;
    function swap_ranges(first1, last1, first2) {
        for (; !first1.equals(last1); first1 = first1.next()) {
            first1.swap(first2);
            first2 = first2.next();
        }
        return first2;
    }
    std.swap_ranges = swap_ranges;
    function reverse(first, last) {
        while (first.equals(last) == false && first.equals((last = last.prev())) == false) {
            first.swap(last);
            first = first.next();
        }
    }
    std.reverse = reverse;
    function reverse_copy(first, last, result) {
        while (!last.equals(first)) {
            last = last.prev();
            result.value = last.value;
            result = result.next();
        }
        return result;
    }
    std.reverse_copy = reverse_copy;
    function rotate(first, middle, last) {
        var next = middle;
        while (next.equals(last) == false) {
            first.swap(next);
            first = first.next();
            next = next.next();
            if (first.equals(middle))
                break;
        }
        return first;
    }
    std.rotate = rotate;
    function rotate_copy(first, middle, last, result) {
        result = copy(middle, last, result);
        return copy(first, middle, result);
    }
    std.rotate_copy = rotate_copy;
    function random_shuffle(first, last) {
        return shuffle(first, last);
    }
    std.random_shuffle = random_shuffle;
    function shuffle(first, last) {
        for (var it = first; !it.equals(last); it = it.next()) {
            var last_index = (last.index() == -1) ? last.source().size() : last.index();
            var rand_index = Math.floor(Math.random() * (last_index - first.index()));
            it.swap(first.advance(rand_index));
        }
    }
    std.shuffle = shuffle;
})(std || (std = {}));
var std;
(function (std) {
    function sort(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        var start = first.index();
        var end = last.index();
        if (start == -1)
            start = first.source().size();
        if (end == -1)
            end = first.source().size();
        _Quick_sort(first.source(), Math.min(start, end), Math.max(start, end), compare);
    }
    std.sort = sort;
    function stable_sort(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        _Stable_quick_sort(first.source(), first.index(), last.index() - 1, compare);
    }
    std.stable_sort = stable_sort;
    function partial_sort(first, middle, last, compare) {
        if (compare === void 0) { compare = std.less; }
        _Selection_sort(first.source(), first.index(), middle.index(), last.index(), compare);
    }
    std.partial_sort = partial_sort;
    function partial_sort_copy(first, last, result_first, result_last, comp) {
        if (comp === void 0) { comp = std.less; }
        var input_size = std.distance(first, last);
        var result_size = std.distance(result_first, result_last);
        var vector = new std.Vector(first, last);
        sort(vector.begin(), vector.end(), comp);
        if (input_size > result_size)
            result_first = std.copy(vector.begin(), vector.begin().advance(result_size), result_first);
        else
            result_first = std.copy(vector.begin(), vector.end(), result_first);
        return result_first;
    }
    std.partial_sort_copy = partial_sort_copy;
    function nth_element(first, nth, last, comp) {
        if (comp === void 0) { comp = std.less; }
        nth.index();
        sort(first, last, comp);
    }
    std.nth_element = nth_element;
    function is_sorted(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        if (first.equals(last))
            return true;
        for (var next_2 = first.next(); !next_2.equals(last); next_2 = next_2.next()) {
            if (!(std.equal_to(next_2.value, first.value) || compare(first.value, next_2.value)))
                return false;
            first = first.next();
        }
        return true;
    }
    std.is_sorted = is_sorted;
    function is_sorted_until(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        if (first.equals(last))
            return first;
        for (var next_3 = first.next(); !next_3.equals(last); next_3 = next_3.next()) {
            if (!(std.equal_to(next_3.value, first.value) || compare(first.value, next_3.value)))
                return next_3;
            first = first.next();
        }
        return last;
    }
    std.is_sorted_until = is_sorted_until;
    function _Quick_sort(container, start, end, compare) {
        var size = end - start;
        if (size <= 0)
            return;
        var pivotIndex = Math.floor(size / 2);
        var pivot = container.at(start + pivotIndex);
        if (pivotIndex != 0)
            _Swap_array_element(container, start + pivotIndex, start);
        var i = 1;
        for (var j = 1; j < size; ++j)
            if (compare(container.at(start + j), pivot)) {
                _Swap_array_element(container, start + j, start + i);
                ++i;
            }
        _Swap_array_element(container, start, start + i - 1);
        _Quick_sort(container, start, start + i - 1, compare);
        _Quick_sort(container, start + i, end, compare);
    }
    function _Stable_quick_sort(container, first, last, compare) {
        if (last == -2)
            last = container.size() - 1;
        if (first >= last)
            return;
        var index = _Stable_quick_sort_partition(container, first, last, compare);
        _Stable_quick_sort(container, first, index - 1, compare);
        _Stable_quick_sort(container, index + 1, last, compare);
    }
    function _Stable_quick_sort_partition(container, first, last, compare) {
        var standard = container.at(first);
        var i = first;
        var j = last + 1;
        while (true) {
            while (compare(container.at(++i), standard))
                if (i == last)
                    break;
            while (compare(standard, container.at(--j)))
                if (j == first)
                    break;
            if (i >= j)
                break;
            if (std.equal_to(container.at(i), container.at(j)) == false)
                _Swap_array_element(container, i, j);
        }
        if (std.equal_to(container.at(first), container.at(j)) == false)
            _Swap_array_element(container, first, j);
        return j;
    }
    function _Swap_array_element(container, i, j) {
        var supplement = container.at(i);
        container.set(i, container.at(j));
        container.set(j, supplement);
    }
    function _Selection_sort(container, first, middle, last, compare) {
        if (last == -1)
            last = container.size();
        for (var i = first; i < middle; i++) {
            var min_index = i;
            for (var j = i + 1; j < last; j++)
                if (compare(container.at(j), container.at(min_index)))
                    min_index = j;
            if (i != min_index)
                _Swap_array_element(container, i, min_index);
        }
    }
})(std || (std = {}));
var std;
(function (std) {
    function make_heap(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        var heap_compare = function (x, y) {
            return !compare(x, y);
        };
        std.sort(first, last, heap_compare);
    }
    std.make_heap = make_heap;
    function push_heap(first, last, comp) {
        if (comp === void 0) { comp = std.less; }
        var last_item_it = last.prev();
        var less_it = null;
        for (var it = first; !it.equals(last_item_it); it = it.next()) {
            if (comp(it.value, last_item_it.value)) {
                less_it = it;
                break;
            }
        }
        if (less_it != null) {
            var container = last_item_it.source();
            container.insert(less_it, last_item_it.value);
            container.erase(last_item_it);
        }
    }
    std.push_heap = push_heap;
    function pop_heap(first, last, comp) {
        if (comp === void 0) { comp = std.less; }
        if (is_heap(first, last, comp) == false)
            throw new std.InvalidArgument("Elements are not sorted by heap.");
        var container = first.source();
        container.insert(last, first.value);
        container.erase(first);
    }
    std.pop_heap = pop_heap;
    function is_heap(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        var it = is_heap_until(first, last, compare);
        return it.equals(last);
    }
    std.is_heap = is_heap;
    function is_heap_until(first, last, comp) {
        if (comp === void 0) { comp = std.less; }
        var prev = first;
        for (var it = first.next(); !it.equals(last); it = it.next()) {
            if (comp(prev.value, it.value) == true)
                return it;
            prev = it;
        }
        return last;
    }
    std.is_heap_until = is_heap_until;
    function sort_heap(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        std.sort(first, last, compare);
    }
    std.sort_heap = sort_heap;
})(std || (std = {}));
var std;
(function (std) {
    function lower_bound(first, last, val, compare) {
        if (compare === void 0) { compare = std.less; }
        var count = std.distance(first, last);
        while (count > 0) {
            var step = Math.floor(count / 2);
            var it = std.advance(first, step);
            if (compare(it.value, val)) {
                first = it.next();
                count -= step + 1;
            }
            else
                count = step;
        }
        return first;
    }
    std.lower_bound = lower_bound;
    function upper_bound(first, last, val, compare) {
        if (compare === void 0) { compare = std.less; }
        var count = std.distance(first, last);
        while (count > 0) {
            var step = Math.floor(count / 2);
            var it = std.advance(first, step);
            if (!compare(val, it.value)) {
                first = it.next();
                count -= step + 1;
            }
            else
                count = step;
        }
        return first;
    }
    std.upper_bound = upper_bound;
    function equal_range(first, last, val, compare) {
        if (compare === void 0) { compare = std.less; }
        var it = lower_bound(first, last, val, compare);
        return std.make_pair(it, upper_bound(it, last, val, compare));
    }
    std.equal_range = equal_range;
    function binary_search(first, last, val, compare) {
        if (compare === void 0) { compare = std.less; }
        first = lower_bound(first, last, val, compare);
        return !first.equals(last) && !compare(val, first.value);
    }
    std.binary_search = binary_search;
})(std || (std = {}));
var std;
(function (std) {
    function is_partitioned(first, last, pred) {
        while (!first.equals(last) && pred(first.value))
            first = first.next();
        for (; !first.equals(last); first = first.next())
            if (pred(first.value))
                return false;
        return true;
    }
    std.is_partitioned = is_partitioned;
    function partition(first, last, pred) {
        while (!first.equals(last)) {
            while (pred(first.value)) {
                first = first.next();
                if (first.equals(last))
                    return first;
            }
            do {
                last = last.prev();
                if (first.equals(last))
                    return first;
            } while (!pred(last.value));
            first.swap(last);
            first = first.next();
        }
        return last;
    }
    std.partition = partition;
    function stable_partition(first, last, pred) {
        return partition(first, last, pred);
    }
    std.stable_partition = stable_partition;
    function partition_copy(first, last, result_true, result_false, pred) {
        for (; !first.equals(last); first = first.next())
            if (pred(first.value)) {
                result_true.value = first.value;
                result_true = result_true.next();
            }
            else {
                result_false.value = first.value;
                result_false = result_false.next();
            }
        return std.make_pair(result_true, result_false);
    }
    std.partition_copy = partition_copy;
    function partition_point(first, last, pred) {
        var n = std.distance(first, last);
        while (n > 0) {
            var step = Math.floor(n / 2);
            var it = first.advance(step);
            if (pred(it.value)) {
                first = it.next();
                n -= step + 1;
            }
            else
                n = step;
        }
        return first;
    }
    std.partition_point = partition_point;
})(std || (std = {}));
var std;
(function (std) {
    function merge(first1, last1, first2, last2, result, compare) {
        if (compare === void 0) { compare = std.less; }
        while (true) {
            if (first1.equals(last1))
                return std.copy(first2, last2, result);
            else if (first2.equals(last2))
                return std.copy(first1, last1, result);
            if (compare(first1.value, first2.value)) {
                result.value = first1.value;
                first1 = first1.next();
            }
            else {
                result.value = first2.value;
                first2 = first2.next();
            }
            result = result.next();
        }
    }
    std.merge = merge;
    function inplace_merge(first, middle, last, comp) {
        if (comp === void 0) { comp = std.less; }
        var vector = new std.Vector(std.distance(first, last), null);
        merge(first, middle, middle, last, vector.begin(), comp);
        std.copy(vector.begin(), vector.end(), first);
    }
    std.inplace_merge = inplace_merge;
    function includes(first1, last1, first2, last2, compare) {
        if (compare === void 0) { compare = std.less; }
        while (!first2.equals(last2)) {
            if (first1.equals(last1) || compare(first2.value, first1.value))
                return false;
            else if (!compare(first1.value, first2.value))
                first2 = first2.next();
            first1 = first1.next();
        }
        return true;
    }
    std.includes = includes;
    function set_union(first1, last1, first2, last2, result, compare) {
        if (compare === void 0) { compare = std.less; }
        while (true) {
            if (first1.equals(last1))
                return std.copy(first2, last2, result);
            else if (first2.equals(last2))
                return std.copy(first1, last1, result);
            if (compare(first1.value, first2.value)) {
                result.value = first1.value;
                first1 = first1.next();
            }
            else if (compare(first2.value, first1.value)) {
                result.value = first2.value;
                first2 = first2.next();
            }
            else {
                result.value = first1.value;
                first1 = first1.next();
                first2 = first2.next();
            }
            result = result.next();
        }
    }
    std.set_union = set_union;
    function set_intersection(first1, last1, first2, last2, result, compare) {
        if (compare === void 0) { compare = std.less; }
        while (true) {
            if (first1.equals(last1))
                return std.copy(first2, last2, result);
            else if (first2.equals(last2))
                return std.copy(first1, last1, result);
            if (compare(first1.value, first2.value))
                first1 = first1.next();
            else if (compare(first2.value, first1.value))
                first2 = first2.next();
            else {
                result.value = first1.value;
                result = result.next();
                first1 = first1.next();
                first2 = first2.next();
            }
        }
    }
    std.set_intersection = set_intersection;
    function set_difference(first1, last1, first2, last2, result, comp) {
        if (comp === void 0) { comp = std.less; }
        while (!first1.equals(last1) && !first2.equals(last2))
            if (comp(first1.value, first2.value)) {
                result.value = first1.value;
                result = result.next();
                first1 = first1.next();
            }
            else if (comp(first2.value, first1.value))
                first2 = first2.next();
            else {
                first1 = first1.next();
                first2 = first2.next();
            }
        return std.copy(first1, last1, result);
    }
    std.set_difference = set_difference;
    function set_symmetric_difference(first1, last1, first2, last2, result, compare) {
        if (compare === void 0) { compare = std.less; }
        while (true) {
            if (first1.equals(last1))
                return std.copy(first2, last2, result);
            else if (first2.equals(last2))
                return std.copy(first1, last1, result);
            if (compare(first1.value, first2.value)) {
                result.value = first1.value;
                result = result.next();
                first1 = first1.next();
            }
            else if (compare(first2.value, first1.value)) {
                result.value = first2.value;
                result = result.next();
                first2 = first2.next();
            }
            else {
                first1 = first1.next();
                first2 = first2.next();
            }
        }
    }
    std.set_symmetric_difference = set_symmetric_difference;
})(std || (std = {}));
var std;
(function (std) {
    function min() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var minimum = args[0];
        for (var i = 1; i < args.length; i++)
            if (std.less(args[i], minimum))
                minimum = args[i];
        return minimum;
    }
    std.min = min;
    function max() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var maximum = args[0];
        for (var i = 1; i < args.length; i++)
            if (std.greater(args[i], maximum))
                maximum = args[i];
        return maximum;
    }
    std.max = max;
    function minmax() {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            args[_i] = arguments[_i];
        }
        var minimum = args[0];
        var maximum = args[0];
        for (var i = 1; i < args.length; i++) {
            if (std.less(args[i], minimum))
                minimum = args[i];
            if (std.greater(args[i], maximum))
                maximum = args[i];
        }
        return std.make_pair(minimum, maximum);
    }
    std.minmax = minmax;
    function min_element(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        var smallest = first;
        first = first.next();
        for (; !first.equals(last); first = first.next())
            if (compare(first.value, smallest.value))
                smallest = first;
        return smallest;
    }
    std.min_element = min_element;
    function max_element(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        var largest = first;
        first = first.next();
        for (; !first.equals(last); first = first.next())
            if (!compare(first.value, largest.value))
                largest = first;
        return largest;
    }
    std.max_element = max_element;
    function minmax_element(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        var smallest = first;
        var largest = first;
        first = first.next();
        for (; !first.equals(last); first = first.next()) {
            if (compare(first.value, smallest.value))
                smallest = first;
            if (!compare(first.value, largest.value))
                largest = first;
        }
        return std.make_pair(smallest, largest);
    }
    std.minmax_element = minmax_element;
    function clamp(v, lo, hi, comp) {
        if (comp === void 0) { comp = std.less; }
        var vec = new std.Vector([v, lo, hi]);
        std.sort(vec.begin(), vec.end(), comp);
        return vec.at(1);
    }
    std.clamp = clamp;
    function is_permutation(first1, last1, first2, pred) {
        if (pred === void 0) { pred = std.equal_to; }
        var pair = std.mismatch(first1, last1, first2);
        first1 = pair.first;
        first2 = pair.second;
        if (first1.equals(last1))
            return true;
        var last2 = first2.advance(std.distance(first1, last1));
        var _loop_1 = function (it) {
            var lamda = function (val) {
                return pred(val, it.value);
            };
            if (std.find_if(first1, it, lamda).equals(it)) {
                var n = std.count(first2, last2, it.value);
                if (n == 0 || std.count(it, last1, it.value) != n)
                    return { value: false };
            }
        };
        for (var it = first1; !it.equals(last1); it = it.next()) {
            var state_1 = _loop_1(it);
            if (typeof state_1 === "object")
                return state_1.value;
        }
        return true;
    }
    std.is_permutation = is_permutation;
    function prev_permutation(first, last, comp) {
        if (comp === void 0) { comp = std.less; }
        if (first.equals(last) == true)
            return false;
        var i = last.prev();
        if (first.equals(i) == true)
            return false;
        while (true) {
            var x = i;
            var y = void 0;
            i = i.prev();
            if (comp(x.value, i.value) == true) {
                y = last.prev();
                while (comp(y.value, i.value) == false)
                    y = y.prev();
                std.iter_swap(i, y);
                std.reverse(x, last);
                return true;
            }
            if (i.equals(first) == true) {
                std.reverse(first, last);
                return false;
            }
        }
    }
    std.prev_permutation = prev_permutation;
    function next_permutation(first, last, compare) {
        if (compare === void 0) { compare = std.less; }
        if (first.equals(last) == true)
            return false;
        var i = last.prev();
        if (first.equals(i) == true)
            return false;
        while (true) {
            var x = i;
            var y = void 0;
            i = i.prev();
            if (compare(i.value, x.value) == true) {
                y = last.prev();
                while (compare(i.value, y.value) == false)
                    y = y.prev();
                std.iter_swap(i, y);
                std.reverse(x, last);
                return true;
            }
            if (i.equals(first) == true) {
                std.reverse(first, last);
                return false;
            }
        }
    }
    std.next_permutation = next_permutation;
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var Container = (function () {
            function Container() {
            }
            Container.prototype.clear = function () {
                this.erase(this.begin(), this.end());
            };
            Container.prototype.empty = function () {
                return this.size() == 0;
            };
            Container.prototype[Symbol.iterator] = function () {
                return new base.ForOfAdaptor(this.begin(), this.end());
            };
            Container.prototype.swap = function (obj) {
                var supplement = new std.Vector(this.begin(), this.end());
                this.assign(obj.begin(), obj.end());
                obj.assign(supplement.begin(), supplement.end());
            };
            return Container;
        }());
        base.Container = Container;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var ArrayContainer = (function (_super) {
            __extends(ArrayContainer, _super);
            function ArrayContainer() {
                var _this = _super.call(this) || this;
                _this.begin_ = new base.ArrayIterator(_this, 0);
                _this.end_ = new base.ArrayIterator(_this, -1);
                _this.rend_ = new base.ArrayReverseIterator(_this.begin_);
                return _this;
            }
            ArrayContainer.prototype.begin = function () {
                if (this.empty() == true)
                    return this.end_;
                else
                    return this.begin_;
            };
            ArrayContainer.prototype.end = function () {
                return this.end_;
            };
            ArrayContainer.prototype.rbegin = function () {
                return new base.ArrayReverseIterator(this.end_);
            };
            ArrayContainer.prototype.rend = function () {
                if (this.empty() == true)
                    return new base.ArrayReverseIterator(this.end_);
                else
                    return this.rend_;
            };
            ArrayContainer.prototype.front = function (val) {
                if (val === void 0) { val = null; }
                if (val == null)
                    return this.at(0);
                else
                    this.set(0, val);
            };
            ArrayContainer.prototype.back = function (val) {
                if (val === void 0) { val = null; }
                var index = this.size() - 1;
                if (val == null)
                    return this.at(index);
                else
                    this.set(index, val);
            };
            ArrayContainer.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args[0].source() != this)
                    throw new std.InvalidArgument("Parametric iterator is not this container's own.");
                var ret;
                var is_reverse_iterator = false;
                if (args[0] instanceof base.ArrayReverseIterator) {
                    is_reverse_iterator = true;
                    args[0] = args[0].base().prev();
                }
                if (args.length == 2)
                    ret = this._Insert_by_val(args[0], args[1]);
                else if (args.length == 3 && typeof args[1] == "number")
                    ret = this._Insert_by_repeating_val(args[0], args[1], args[2]);
                else
                    ret = this._Insert_by_range(args[0], args[1], args[2]);
                if (is_reverse_iterator == true)
                    return new base.ArrayReverseIterator(ret.next());
                else
                    return ret;
            };
            ArrayContainer.prototype._Insert_by_val = function (position, val) {
                return this._Insert_by_repeating_val(position, 1, val);
            };
            ArrayContainer.prototype._Insert_by_repeating_val = function (position, n, val) {
                var first = new base._Repeater(0, val);
                var last = new base._Repeater(n);
                return this._Insert_by_range(position, first, last);
            };
            ArrayContainer.prototype.erase = function (first, last) {
                if (last === void 0) { last = first.next(); }
                if (first.source() != this || last.source() != this)
                    throw new std.InvalidArgument("Parametric iterator is not this container's own.");
                var ret;
                var is_reverse_iterator = false;
                if (first instanceof base.ArrayReverseIterator) {
                    is_reverse_iterator = true;
                    var first_it = last.base();
                    var last_it = first.base();
                    first = first_it;
                    last = last_it;
                }
                ret = this._Erase_by_range(first, last);
                if (is_reverse_iterator == true)
                    return new base.ArrayReverseIterator(ret.next());
                else
                    return ret;
            };
            return ArrayContainer;
        }(base.Container));
        base.ArrayContainer = ArrayContainer;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var Iterator = (function () {
            function Iterator() {
            }
            return Iterator;
        }());
        base.Iterator = Iterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base_1) {
        var ReverseIterator = (function (_super) {
            __extends(ReverseIterator, _super);
            function ReverseIterator(base) {
                var _this = _super.call(this) || this;
                _this.base_ = base.prev();
                return _this;
            }
            ReverseIterator.prototype.source = function () {
                return this.base_.source();
            };
            ReverseIterator.prototype.base = function () {
                return this.base_.next();
            };
            Object.defineProperty(ReverseIterator.prototype, "value", {
                get: function () {
                    return this.base_.value;
                },
                enumerable: true,
                configurable: true
            });
            ReverseIterator.prototype.prev = function () {
                return this._Create_neighbor(this.base_);
            };
            ReverseIterator.prototype.next = function () {
                return this._Create_neighbor(this.base().prev());
            };
            ReverseIterator.prototype.advance = function (n) {
                return this._Create_neighbor(this.base().advance(-n));
            };
            ReverseIterator.prototype.equals = function (obj) {
                return this.base_.equals(obj.base_);
            };
            ReverseIterator.prototype.swap = function (obj) {
                this.base_.swap(obj.base_);
            };
            return ReverseIterator;
        }(base_1.Iterator));
        base_1.ReverseIterator = ReverseIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var ArrayIterator = (function (_super) {
            __extends(ArrayIterator, _super);
            function ArrayIterator(source, index) {
                var _this = _super.call(this) || this;
                _this.source_ = source;
                _this.index_ = index;
                return _this;
            }
            ArrayIterator.prototype.source = function () {
                return this.source_;
            };
            ;
            ArrayIterator.prototype.index = function () {
                return this.index_;
            };
            Object.defineProperty(ArrayIterator.prototype, "value", {
                get: function () {
                    return this.source().at(this.index_);
                },
                set: function (val) {
                    this.source().set(this.index_, val);
                },
                enumerable: true,
                configurable: true
            });
            ;
            ;
            ArrayIterator.prototype.prev = function () {
                if (this.index_ == -1)
                    return new ArrayIterator(this.source(), this.source_.size() - 1);
                else if (this.index_ - 1 < 0)
                    return this.source().end();
                else
                    return new ArrayIterator(this.source(), this.index_ - 1);
            };
            ArrayIterator.prototype.next = function () {
                if (this.index_ >= this.source_.size() - 1)
                    return this.source().end();
                else
                    return new ArrayIterator(this.source(), this.index_ + 1);
            };
            ArrayIterator.prototype.advance = function (n) {
                var new_index;
                if (n < 0 && this.index_ == -1)
                    new_index = this.source_.size() + n;
                else
                    new_index = this.index_ + n;
                if (new_index < 0 || new_index >= this.source_.size())
                    return this.source().end();
                else
                    return new ArrayIterator(this.source(), new_index);
            };
            ArrayIterator.prototype.equals = function (obj) {
                return std.equal_to(this.source_, obj.source_) && this.index_ == obj.index_;
            };
            ArrayIterator.prototype.swap = function (obj) {
                _a = __read([obj.value, this.value], 2), this.value = _a[0], obj.value = _a[1];
                var _a;
            };
            ;
            return ArrayIterator;
        }(base.Iterator));
        base.ArrayIterator = ArrayIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
(function (std) {
    var base;
    (function (base_2) {
        var ArrayReverseIterator = (function (_super) {
            __extends(ArrayReverseIterator, _super);
            function ArrayReverseIterator(base) {
                return _super.call(this, base) || this;
            }
            ArrayReverseIterator.prototype._Create_neighbor = function (base) {
                return new ArrayReverseIterator(base);
            };
            ArrayReverseIterator.prototype.index = function () {
                return this.base_.index();
            };
            Object.defineProperty(ArrayReverseIterator.prototype, "value", {
                get: function () {
                    return this.base_.value;
                },
                set: function (val) {
                    this.base_.value = val;
                },
                enumerable: true,
                configurable: true
            });
            return ArrayReverseIterator;
        }(base_2.ReverseIterator));
        base_2.ArrayReverseIterator = ArrayReverseIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var Vector = (function (_super) {
        __extends(Vector, _super);
        function Vector() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            _this.data_ = [];
            if (args.length == 0) {
            }
            else if (args.length == 1 && args[0] instanceof Array) {
                var array = args[0];
                _this.data_ = array.slice();
            }
            else if (args.length == 1 && args[0] instanceof std.Vector) {
                _this.data_ = args[0].data_.slice();
            }
            else if (args.length == 2) {
                _this.assign(args[0], args[1]);
            }
            return _this;
        }
        Vector.prototype.assign = function (first, second) {
            this.clear();
            this.insert(this.end(), first, second);
        };
        Vector.prototype.clear = function () {
            this.erase(this.begin(), this.end());
        };
        Vector.prototype.resize = function (n) {
            this.data_.length = n;
        };
        Vector.prototype.size = function () {
            return this.data_.length;
        };
        Vector.prototype.at = function (index) {
            if (index < this.size())
                return this.data_[index];
            else
                throw new std.OutOfRange("Target index is greater than Vector's size.");
        };
        Vector.prototype.set = function (index, val) {
            if (index >= this.size())
                throw new std.OutOfRange("Target index is greater than Vector's size.");
            var prev = this.data_[index];
            this.data_[index] = val;
            return prev;
        };
        Vector.prototype.data = function () {
            return this.data_;
        };
        Vector.prototype[Symbol.iterator] = function () {
            return this.data_[Symbol.iterator]();
        };
        Vector.prototype.push = function () {
            var items = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                items[_i] = arguments[_i];
            }
            return (_a = this.data_).push.apply(_a, __spread(items));
            var _a;
        };
        Vector.prototype.push_back = function (val) {
            this.data_.push(val);
        };
        Vector.prototype._Insert_by_range = function (position, first, last) {
            if (position.index() == -1) {
                for (; !first.equals(last); first = first.next())
                    this.data_.push(first.value);
                return this.begin();
            }
            else {
                var spliced_array = this.data_.splice(position.index());
                for (; !first.equals(last); first = first.next())
                    this.data_.push(first.value);
                (_a = this.data_).push.apply(_a, __spread(spliced_array));
                return position;
            }
            var _a;
        };
        Vector.prototype.pop_back = function () {
            this.data_.pop();
        };
        Vector.prototype._Erase_by_range = function (first, last) {
            if (first.index() == -1)
                return first;
            if (last.index() == -1) {
                this.data_.splice(first.index());
                return this.end();
            }
            else
                this.data_.splice(first.index(), last.index() - first.index());
            return first;
        };
        Vector.prototype.equals = function (obj) {
            return this.data_ == obj.data_;
        };
        Vector.prototype.swap = function (obj) {
            _a = __read([obj.data_, this.data_], 2), this.data_ = _a[0], obj.data_ = _a[1];
            var _a;
        };
        return Vector;
    }(std.base.ArrayContainer));
    std.Vector = Vector;
})(std || (std = {}));
(function (std) {
    var Vector;
    (function (Vector) {
        Vector.Iterator = std.base.ArrayIterator;
        Vector.ReverseIterator = std.base.ArrayReverseIterator;
        Vector.iterator = Vector.Iterator;
        Vector.reverse_iterator = Vector.ReverseIterator;
    })(Vector = std.Vector || (std.Vector = {}));
})(std || (std = {}));
var std;
(function (std) {
    var Deque = (function (_super) {
        __extends(Deque, _super);
        function Deque() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            if (args.length == 0) {
                _this.clear();
            }
            if (args.length == 1 && args[0] instanceof Array) {
                var array = args[0];
                var first = new std.base._NativeArrayIterator(array, 0);
                var last = new std.base._NativeArrayIterator(array, array.length);
                _this.assign(first, last);
            }
            else if (args.length == 1 && args[0] instanceof Deque) {
                var container = args[0];
                _this.assign(container.begin(), container.end());
            }
            else if (args.length == 2) {
                _this.assign(args[0], args[1]);
            }
            return _this;
        }
        Deque.prototype.assign = function (first, second) {
            this.clear();
            this.insert(this.end(), first, second);
        };
        Deque.prototype.clear = function () {
            this.matrix_ = [[]];
            this.size_ = 0;
            this.capacity_ = Deque.MIN_CAPACITY;
        };
        Deque.prototype.reserve = function (capacity) {
            if (capacity < this.capacity_)
                return;
            var matrix = [[]];
            var col_size = this._Compute_col_size(capacity);
            for (var r = 0; r < this.matrix_.length; r++) {
                var row = this.matrix_[r];
                for (var c = 0; c < row.length; c++) {
                    var new_row = matrix[matrix.length - 1];
                    if (matrix.length < Deque.ROW_SIZE && new_row.length == col_size) {
                        new_row = [];
                        matrix.push(new_row);
                    }
                    new_row.push(row[c]);
                }
            }
            this.matrix_ = matrix;
            this.capacity_ = capacity;
        };
        Deque.prototype.resize = function (n) {
            var expansion = n - this.size();
            if (expansion > 0)
                this.insert(this.end(), expansion, undefined);
            else if (expansion < 0)
                this.erase(this.end().advance(-expansion), this.end());
        };
        Deque.prototype.shrink_to_fit = function () {
            this.reserve(this.size());
        };
        Deque.prototype.size = function () {
            return this.size_;
        };
        Deque.prototype.capacity = function () {
            return this.capacity_;
        };
        Deque.prototype[Symbol.iterator] = function () {
            return new std.base._DequeForOfAdaptor(this.matrix_);
        };
        Deque.prototype.at = function (index) {
            if (index < this.size() && index >= 0) {
                var indexPair = this._Fetch_index(index);
                return this.matrix_[indexPair.first][indexPair.second];
            }
            else
                throw new std.OutOfRange("Target index is greater than Deque's size.");
        };
        Deque.prototype.set = function (index, val) {
            if (index >= this.size() || index < 0)
                throw new std.OutOfRange("Target index is greater than Deque's size.");
            var indexPair = this._Fetch_index(index);
            this.matrix_[indexPair.first][indexPair.second] = val;
        };
        Deque.prototype._Fetch_index = function (index) {
            var row;
            for (row = 0; row < this.matrix_.length; row++) {
                var array = this.matrix_[row];
                if (index < array.length)
                    break;
                index -= array.length;
            }
            if (row == this.matrix_.length)
                row--;
            return std.make_pair(row, index);
        };
        Deque.prototype._Compute_col_size = function (capacity) {
            if (capacity === void 0) { capacity = this.capacity_; }
            return Math.floor(capacity / Deque.ROW_SIZE);
        };
        Deque.prototype.push = function () {
            var items = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                items[_i] = arguments[_i];
            }
            if (items.length == 0)
                return this.size();
            var first = new std.base._NativeArrayIterator(items, 0);
            var last = new std.base._NativeArrayIterator(items, items.length);
            this._Insert_by_range(this.end(), first, last);
            return this.size();
        };
        Deque.prototype.push_front = function (val) {
            this._Try_expand_capacity(this.size_ + 1);
            this._Try_add_row_at_front();
            this.matrix_[0].unshift(val);
            this.size_++;
        };
        Deque.prototype.push_back = function (val) {
            this._Try_expand_capacity(this.size_ + 1);
            this._Try_add_row_at_back();
            this.matrix_[this.matrix_.length - 1].push(val);
            this.size_++;
        };
        Deque.prototype.pop_front = function () {
            if (this.empty() == true)
                return;
            this.matrix_[0].shift();
            if (this.matrix_[0].length == 0 && this.matrix_.length > 1)
                this.matrix_.shift();
            this.size_--;
        };
        Deque.prototype.pop_back = function () {
            if (this.empty() == true)
                return;
            var lastArray = this.matrix_[this.matrix_.length - 1];
            lastArray.pop();
            if (lastArray.length == 0 && this.matrix_.length > 1)
                this.matrix_.pop();
            this.size_--;
        };
        Deque.prototype._Insert_by_range = function (pos, first, last) {
            var size = this.size_ + std.distance(first, last);
            if (size == this.size_)
                return pos;
            if (pos.equals(this.end()) == true) {
                this._Try_expand_capacity(size);
                this._Insert_to_end(first, last);
                pos = new std.base.ArrayIterator(this, this.size_);
            }
            else {
                if (size > this.capacity_) {
                    var deque_1 = new Deque();
                    deque_1.reserve(Math.max(size, Math.floor(this.capacity_ * Deque.MAGNIFIER)));
                    deque_1._Insert_to_end(this.begin(), pos);
                    deque_1._Insert_to_end(first, last);
                    deque_1._Insert_to_end(pos, this.end());
                    this.swap(deque_1);
                }
                else
                    this._Insert_to_middle(pos, first, last);
            }
            this.size_ = size;
            return pos;
        };
        Deque.prototype._Insert_to_middle = function (pos, first, last) {
            var col_size = this._Compute_col_size();
            var indexes = this._Fetch_index(pos.index());
            var row = this.matrix_[indexes.first];
            var col = indexes.second;
            var back_items = row.splice(col);
            for (; !first.equals(last); first = first.next()) {
                if (row.length == col_size && this.matrix_.length < Deque.ROW_SIZE) {
                    row = new Array();
                    var spliced_array = this.matrix_.splice(++indexes.first);
                    this.matrix_.push(row);
                    (_a = this.matrix_).push.apply(_a, __spread(spliced_array));
                }
                row.push(first.value);
            }
            for (var i = 0; i < back_items.length; i++) {
                if (row.length == col_size && this.matrix_.length < Deque.ROW_SIZE) {
                    row = new Array();
                    var spliced_array = this.matrix_.splice(++indexes.first);
                    this.matrix_.push(row);
                    (_b = this.matrix_).push.apply(_b, __spread(spliced_array));
                }
                row.push(back_items[i]);
            }
            var _a, _b;
        };
        Deque.prototype._Insert_to_end = function (first, last) {
            for (; !first.equals(last); first = first.next()) {
                this._Try_add_row_at_back();
                this.matrix_[this.matrix_.length - 1].push(first.value);
            }
        };
        Deque.prototype._Try_expand_capacity = function (size) {
            if (size <= this.capacity_)
                return false;
            size = Math.max(size, Math.floor(this.capacity_ * Deque.MAGNIFIER));
            this.reserve(size);
            return true;
        };
        Deque.prototype._Try_add_row_at_front = function () {
            var col_size = this._Compute_col_size();
            if (this.matrix_[0].length >= col_size && this.matrix_.length < Deque.ROW_SIZE) {
                this.matrix_ = (_a = [[]]).concat.apply(_a, __spread(this.matrix_));
                return true;
            }
            else
                return false;
            var _a;
        };
        Deque.prototype._Try_add_row_at_back = function () {
            var col_size = this._Compute_col_size();
            if (this.matrix_[this.matrix_.length - 1].length >= col_size && this.matrix_.length < Deque.ROW_SIZE) {
                this.matrix_.push([]);
                return true;
            }
            else
                return false;
        };
        Deque.prototype._Erase_by_range = function (first, last) {
            if (first.index() == -1)
                return first;
            var size;
            if (last.index() == -1)
                size = this.size() - first.index();
            else
                size = last.index() - first.index();
            this.size_ -= size;
            var first_row = null;
            var second_row = null;
            var i = 0;
            while (size != 0) {
                var indexes = this._Fetch_index(first.index());
                var row = this.matrix_[indexes.first];
                var col = indexes.second;
                var my_delete_size = Math.min(size, row.length - col);
                row.splice(col, my_delete_size);
                if (row.length != 0)
                    if (i == 0)
                        first_row = row;
                    else
                        second_row = row;
                if (row.length == 0 && this.matrix_.length > 1)
                    this.matrix_.splice(indexes.first, 1);
                size -= my_delete_size;
                i++;
            }
            if (first_row != null && second_row != null
                && first_row.length + second_row.length <= this._Compute_col_size()) {
                first_row.push.apply(first_row, __spread(second_row));
                this.matrix_.splice(this.matrix_.indexOf(second_row), 1);
            }
            if (last.index() == -1)
                return this.end();
            else
                return first;
        };
        Deque.prototype.swap = function (obj) {
            _a = __read([obj.matrix_, this.matrix_], 2), this.matrix_ = _a[0], obj.matrix_ = _a[1];
            _b = __read([obj.size_, this.size_], 2), this.size_ = _b[0], obj.size_ = _b[1];
            _c = __read([obj.capacity_, this.capacity_], 2), this.capacity_ = _c[0], obj.capacity_ = _c[1];
            var _a, _b, _c;
        };
        Object.defineProperty(Deque, "ROW_SIZE", {
            get: function () { return 8; },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(Deque, "MIN_CAPACITY", {
            get: function () { return 36; },
            enumerable: true,
            configurable: true
        });
        Object.defineProperty(Deque, "MAGNIFIER", {
            get: function () { return 1.5; },
            enumerable: true,
            configurable: true
        });
        return Deque;
    }(std.base.ArrayContainer));
    std.Deque = Deque;
})(std || (std = {}));
(function (std) {
    var Deque;
    (function (Deque) {
        Deque.Iterator = std.base.ArrayIterator;
        Deque.ReverseIterator = std.base.ArrayReverseIterator;
        Deque.iterator = Deque.Iterator;
        Deque.reverse_iterator = Deque.ReverseIterator;
    })(Deque = std.Deque || (std.Deque = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _ListContainer = (function (_super) {
            __extends(_ListContainer, _super);
            function _ListContainer() {
                var _this = _super.call(this) || this;
                _this.end_ = _this._Create_iterator(null, null, null);
                _this.end_.prev_ = _this.end_;
                _this.end_.next_ = _this.end_;
                _this._Set_begin(_this.end_);
                _this.size_ = 0;
                return _this;
            }
            _ListContainer.prototype._Set_begin = function (it) {
                this.begin_ = it;
            };
            _ListContainer.prototype.assign = function (first, last) {
                this.clear();
                this.insert(this.end(), first, last);
            };
            _ListContainer.prototype.clear = function () {
                this._Set_begin(this.end_);
                this.end_.prev_ = (this.end_);
                this.end_.next_ = (this.end_);
                this.size_ = 0;
            };
            _ListContainer.prototype.begin = function () {
                return this.begin_;
            };
            _ListContainer.prototype.end = function () {
                return this.end_;
            };
            _ListContainer.prototype.size = function () {
                return this.size_;
            };
            _ListContainer.prototype.front = function (val) {
                if (val === void 0) { val = null; }
                if (val == null)
                    return this.begin_.value;
                else
                    this.begin_.value_ = val;
            };
            _ListContainer.prototype.back = function (val) {
                if (val === void 0) { val = null; }
                var it = this.end().prev();
                if (val == null)
                    return it.value;
                else
                    it.value_ = val;
            };
            _ListContainer.prototype.push_front = function (val) {
                this.insert(this.begin_, val);
            };
            _ListContainer.prototype.push_back = function (val) {
                this.insert(this.end_, val);
            };
            _ListContainer.prototype.pop_front = function () {
                this.erase(this.begin_);
            };
            _ListContainer.prototype.pop_back = function () {
                this.erase(this.end_.prev());
            };
            _ListContainer.prototype.push = function () {
                var items = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    items[_i] = arguments[_i];
                }
                if (items.length == 0)
                    return this.size();
                var first = new base._NativeArrayIterator(items, 0);
                var last = new base._NativeArrayIterator(items, items.length);
                this._Insert_by_range(this.end(), first, last);
                return this.size();
            };
            _ListContainer.prototype.insert = function (pos) {
                var args = [];
                for (var _i = 1; _i < arguments.length; _i++) {
                    args[_i - 1] = arguments[_i];
                }
                if (pos.source() != this.end_.source())
                    throw new std.InvalidArgument("Parametric iterator is not this container's own.");
                if (args.length == 1)
                    return this._Insert_by_repeating_val(pos, 1, args[0]);
                else if (args.length == 2 && typeof args[0] == "number")
                    return this._Insert_by_repeating_val(pos, args[0], args[1]);
                else
                    return this._Insert_by_range(pos, args[0], args[1]);
            };
            _ListContainer.prototype._Insert_by_repeating_val = function (position, n, val) {
                var first = new base._Repeater(0, val);
                var last = new base._Repeater(n);
                return this._Insert_by_range(position, first, last);
            };
            _ListContainer.prototype._Insert_by_range = function (position, begin, end) {
                var prev = position.prev();
                var first = null;
                var size = 0;
                for (var it = begin; it.equals(end) == false; it = it.next()) {
                    var item = this._Create_iterator(prev, null, it.value);
                    if (size == 0)
                        first = item;
                    prev.next_ = item;
                    prev = item;
                    size++;
                }
                if (position.equals(this.begin()) == true)
                    this._Set_begin(first);
                prev.next_ = position;
                position.prev_ = prev;
                this.size_ += size;
                return first;
            };
            _ListContainer.prototype.erase = function (first, last) {
                if (last === void 0) { last = first.next(); }
                return this._Erase_by_range(first, last);
            };
            _ListContainer.prototype._Erase_by_range = function (first, last) {
                if (first.source() != this.end_.source() || last.source() != this.end_.source())
                    throw new std.InvalidArgument("Parametric iterator is not this container's own.");
                var prev = first.prev();
                var size = std.distance(first, last);
                prev.next_ = (last);
                last.prev_ = (prev);
                this.size_ -= size;
                if (first.equals(this.begin_))
                    this._Set_begin(last);
                return last;
            };
            _ListContainer.prototype.swap = function (obj) {
                _a = __read([obj.begin_, this.begin_], 2), this.begin_ = _a[0], obj.begin_ = _a[1];
                _b = __read([obj.end_, this.end_], 2), this.end_ = _b[0], obj.end_ = _b[1];
                _c = __read([obj.size_, this.size_], 2), this.size_ = _c[0], obj.size_ = _c[1];
                var _a, _b, _c;
            };
            return _ListContainer;
        }(base.Container));
        base._ListContainer = _ListContainer;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _ListIteratorBase = (function (_super) {
            __extends(_ListIteratorBase, _super);
            function _ListIteratorBase(prev, next, value) {
                var _this = _super.call(this) || this;
                _this.prev_ = prev;
                _this.next_ = next;
                _this.value_ = value;
                return _this;
            }
            _ListIteratorBase.prototype.prev = function () {
                return this.prev_;
            };
            _ListIteratorBase.prototype.next = function () {
                return this.next_;
            };
            _ListIteratorBase.prototype.advance = function (step) {
                var it = this;
                if (step >= 0) {
                    for (var i = 0; i < step; i++) {
                        it = it.next();
                        if (it.equals(this.source().end()))
                            return it;
                    }
                }
                else {
                    for (var i = 0; i < step; i++) {
                        it = it.prev();
                        if (it.equals(this.source().end()))
                            return it;
                    }
                }
                return it;
            };
            Object.defineProperty(_ListIteratorBase.prototype, "value", {
                get: function () {
                    return this.value_;
                },
                enumerable: true,
                configurable: true
            });
            _ListIteratorBase.prototype.equals = function (obj) {
                return this == obj;
            };
            _ListIteratorBase.prototype.swap = function (obj) {
                var source = this.source();
                var supp_prev = this.prev_;
                var supp_next = this.next_;
                this.prev_ = obj.prev_;
                this.next_ = obj.next_;
                obj.prev_ = supp_prev;
                obj.next_ = supp_next;
                if (source.end() == this)
                    source.end_ = obj;
                else if (source.end() == obj)
                    source.end_ = this;
                if (source.begin() == this)
                    source.begin_ = obj;
                else if (source.begin() == obj)
                    source.begin_ = this;
            };
            return _ListIteratorBase;
        }(base.Iterator));
        base._ListIteratorBase = _ListIteratorBase;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var List = (function (_super) {
        __extends(List, _super);
        function List() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            _this.ptr_ = { value: _this };
            _this.end_.source_ptr_ = _this.ptr_;
            if (args.length == 0) {
            }
            else if (args.length == 1 && args[0] instanceof Array) {
                var array = args[0];
                _this.push.apply(_this, __spread(array));
            }
            else if (args.length == 1 && (args[0] instanceof List)) {
                var container = args[0];
                _this.assign(container.begin(), container.end());
            }
            else if (args.length == 2) {
                _this.assign(args[0], args[1]);
            }
            return _this;
        }
        List.prototype._Create_iterator = function (prev, next, val) {
            return new List.Iterator(this.ptr_, prev, next, val);
        };
        List.prototype._Set_begin = function (it) {
            _super.prototype._Set_begin.call(this, it);
            this.rend_ = new List.ReverseIterator(it);
        };
        List.prototype.assign = function (par1, par2) {
            this.clear();
            this.insert(this.end(), par1, par2);
        };
        List.prototype.rbegin = function () {
            return new List.ReverseIterator(this.end());
        };
        List.prototype.rend = function () {
            return this.rend_;
        };
        List.prototype.insert = function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var ret;
            var is_reverse_iterator = false;
            if (args[0] instanceof List.ReverseIterator) {
                is_reverse_iterator = true;
                args[0] = args[0].base().prev();
            }
            ret = _super.prototype.insert.apply(this, args);
            if (is_reverse_iterator == true)
                return new List.ReverseIterator(ret.next());
            else
                return ret;
        };
        List.prototype.erase = function (first, last) {
            if (last === void 0) { last = first.next(); }
            var ret;
            var is_reverse_iterator = false;
            if (first instanceof List.ReverseIterator) {
                is_reverse_iterator = true;
                var first_it = last.base();
                var last_it = first.base();
                first = first_it;
                last = last_it;
            }
            ret = this._Erase_by_range(first, last);
            if (is_reverse_iterator == true)
                return new List.ReverseIterator(ret.next());
            else
                return ret;
        };
        List.prototype.unique = function (binary_pred) {
            if (binary_pred === void 0) { binary_pred = std.equal_to; }
            var it = this.begin().next();
            while (!it.equals(this.end())) {
                if (binary_pred(it.value, it.prev().value) == true)
                    it = this.erase(it);
                else
                    it = it.next();
            }
        };
        List.prototype.remove = function (val) {
            this.remove_if(function (x) {
                return x == val;
            });
        };
        List.prototype.remove_if = function (pred) {
            var it = this.begin();
            while (!it.equals(this.end())) {
                if (pred(it.value) == true)
                    it = this.erase(it);
                else
                    it = it.next();
            }
        };
        List.prototype.merge = function (obj, compare) {
            if (compare === void 0) { compare = std.less; }
            if (this == obj)
                return;
            var it = this.begin();
            while (obj.empty() == false) {
                var first = obj.begin();
                while (!it.equals(this.end()) && compare(it.value, first.value) == true)
                    it = it.next();
                this.splice(it, obj, first);
            }
        };
        List.prototype.splice = function (position, obj, begin, end) {
            if (begin === void 0) { begin = null; }
            if (end === void 0) { end = null; }
            if (begin == null) {
                begin = obj.begin();
                end = obj.end();
            }
            else if (end == null) {
                end = begin.next();
            }
            this.insert(position, begin, end);
            obj.erase(begin, end);
        };
        List.prototype.sort = function (compare) {
            if (compare === void 0) { compare = std.less; }
            this._Quick_sort(this.begin(), this.end().prev(), compare);
        };
        List.prototype._Quick_sort = function (first, last, compare) {
            if (!first.equals(last) && !last.equals(this.end()) && !first.equals(last.next())) {
                var temp = this._Quick_sort_partition(first, last, compare);
                this._Quick_sort(first, temp.prev(), compare);
                this._Quick_sort(temp.next(), last, compare);
            }
        };
        List.prototype._Quick_sort_partition = function (first, last, compare) {
            var standard = last.value;
            var prev = first.prev();
            var it = first;
            for (; !it.equals(last); it = it.next())
                if (compare(it.value, standard)) {
                    prev = prev.equals(this.end()) ? first : prev.next();
                    _a = __read([it.value, prev.value], 2), prev.value = _a[0], it.value = _a[1];
                }
            prev = prev.equals(this.end()) ? first : prev.next();
            _b = __read([it.value, prev.value], 2), prev.value = _b[0], it.value = _b[1];
            return prev;
            var _a, _b;
        };
        List.prototype.reverse = function () {
            var begin = this.end().prev();
            var prev_of_end = this.begin();
            for (var it = this.begin(); !it.equals(this.end());) {
                var next_4 = it.next();
                _a = __read([it.next_, it.prev_], 2), it.prev_ = _a[0], it.next_ = _a[1];
                it = next_4;
            }
            this._Set_begin(begin);
            this.end().prev_ = prev_of_end;
            this.end().next_ = begin;
            var _a;
        };
        List.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.ptr_, this.ptr_], 2), this.ptr_ = _a[0], obj.ptr_ = _a[1];
            _b = __read([obj.ptr_.value, this.ptr_.value], 2), this.ptr_.value = _b[0], obj.ptr_.value = _b[1];
            var _a, _b;
        };
        return List;
    }(std.base._ListContainer));
    std.List = List;
})(std || (std = {}));
(function (std) {
    var List;
    (function (List) {
        var Iterator = (function (_super) {
            __extends(Iterator, _super);
            function Iterator(sourcePtr, prev, next, value) {
                var _this = _super.call(this, prev, next, value) || this;
                _this.source_ptr_ = sourcePtr;
                return _this;
            }
            Iterator.prototype.source = function () {
                return this.source_ptr_.value;
            };
            Object.defineProperty(Iterator.prototype, "value", {
                get: function () {
                    return this.value_;
                },
                set: function (val) {
                    this.value_ = val;
                },
                enumerable: true,
                configurable: true
            });
            Iterator.prototype.prev = function () {
                return this.prev_;
            };
            Iterator.prototype.next = function () {
                return this.next_;
            };
            Iterator.prototype.advance = function (step) {
                return _super.prototype.advance.call(this, step);
            };
            Iterator.prototype.equals = function (obj) {
                return this == obj;
            };
            Iterator.prototype.swap = function (obj) {
                _super.prototype.swap.call(this, obj);
            };
            return Iterator;
        }(std.base._ListIteratorBase));
        List.Iterator = Iterator;
        var ReverseIterator = (function (_super) {
            __extends(ReverseIterator, _super);
            function ReverseIterator(base) {
                return _super.call(this, base) || this;
            }
            ReverseIterator.prototype._Create_neighbor = function (base) {
                return new ReverseIterator(base);
            };
            Object.defineProperty(ReverseIterator.prototype, "value", {
                get: function () {
                    return this.base_.value;
                },
                set: function (val) {
                    this.base_.value = val;
                },
                enumerable: true,
                configurable: true
            });
            return ReverseIterator;
        }(std.base.ReverseIterator));
        List.ReverseIterator = ReverseIterator;
    })(List = std.List || (std.List = {}));
})(std || (std = {}));
var std;
(function (std) {
    var ForwardList = (function () {
        function ForwardList() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            this.ptr_ = { value: this };
            this.clear();
            if (args.length == 1 && args[0] instanceof Array) {
                var array = args[0];
                var it = this.before_begin();
                try {
                    for (var array_1 = __values(array), array_1_1 = array_1.next(); !array_1_1.done; array_1_1 = array_1.next()) {
                        var val = array_1_1.value;
                        it = this.insert_after(it, val);
                    }
                }
                catch (e_1_1) { e_1 = { error: e_1_1 }; }
                finally {
                    try {
                        if (array_1_1 && !array_1_1.done && (_a = array_1.return)) _a.call(array_1);
                    }
                    finally { if (e_1) throw e_1.error; }
                }
            }
            else if (args.length == 1 && args[0] instanceof ForwardList) {
                this.assign(args[0].begin(), args[0].end());
            }
            else if (args.length == 2)
                this.assign(args[0], args[1]);
            var e_1, _a;
        }
        ForwardList.prototype.assign = function (first, last) {
            this.clear();
            this.insert_after(this.before_begin_, first, last);
        };
        ForwardList.prototype.clear = function () {
            this.end_ = new ForwardList.Iterator(this.ptr_, null, null);
            this.before_begin_ = new ForwardList.Iterator(this.ptr_, this.end_, null);
            this.size_ = 0;
        };
        ForwardList.prototype.size = function () {
            return this.size_;
        };
        ForwardList.prototype.empty = function () {
            return this.size_ == 0;
        };
        ForwardList.prototype.front = function () {
            return this.before_begin_.next().value;
        };
        ForwardList.prototype.before_begin = function () {
            return this.before_begin_;
        };
        ForwardList.prototype.begin = function () {
            return this.before_begin_.next();
        };
        ForwardList.prototype.end = function () {
            return this.end_;
            ;
        };
        ForwardList.prototype[Symbol.iterator] = function () {
            return new std.base.ForOfAdaptor(this.begin(), this.end());
        };
        ForwardList.prototype.push_front = function (val) {
            this.insert_after(this.before_begin_, val);
        };
        ForwardList.prototype.insert_after = function (pos) {
            var args = [];
            for (var _i = 1; _i < arguments.length; _i++) {
                args[_i - 1] = arguments[_i];
            }
            var ret;
            if (args.length == 1)
                ret = this._Insert_by_repeating_val(pos, 1, args[0]);
            else if (typeof args[0] == "number")
                ret = this._Insert_by_repeating_val(pos, args[0], args[1]);
            else
                ret = this._Insert_by_range(pos, args[0], args[1]);
            return ret;
        };
        ForwardList.prototype._Insert_by_repeating_val = function (pos, n, val) {
            var first = new std.base._Repeater(0, val);
            var last = new std.base._Repeater(n);
            return this._Insert_by_range(pos, first, last);
        };
        ForwardList.prototype._Insert_by_range = function (pos, first, last) {
            var nodes = [];
            var count = 0;
            for (; !first.equals(last); first = first.next()) {
                var node = new ForwardList.Iterator(this.ptr_, null, first.value);
                nodes.push(node);
                ++count;
            }
            if (count == 0)
                return pos;
            for (var i = 0; i < count - 1; ++i)
                nodes[i].next_ = nodes[i + 1];
            nodes[nodes.length - 1].next_ = pos.next();
            pos.next_ = nodes[0];
            this.size_ += count;
            return nodes[nodes.length - 1];
        };
        ForwardList.prototype.pop_front = function () {
            this.erase_after(this.before_begin());
        };
        ForwardList.prototype.erase_after = function (first, last) {
            if (last === void 0) { last = first.advance(2); }
            this.size_ -= Math.max(0, std.distance(first, last) - 1);
            first.next_ = last;
            return last;
        };
        ForwardList.prototype.unique = function (binary_pred) {
            if (binary_pred === void 0) { binary_pred = std.equal_to; }
            for (var it = this.begin().next(); !it.equals(this.end()); it = it.next()) {
                var next_it = it.next();
                if (next_it.equals(this.end()))
                    break;
                if (binary_pred(it.value, next_it.value))
                    this.erase_after(it);
            }
        };
        ForwardList.prototype.remove = function (val) {
            this.remove_if(function (elem) {
                return std.equal_to(val, elem);
            });
        };
        ForwardList.prototype.remove_if = function (pred) {
            var count = 0;
            for (var it = this.before_begin(); !it.next().equals(this.end()); it = it.next())
                if (pred(it.next().value) == true) {
                    it.next_ = it.next().next();
                    ++count;
                }
            this.size_ -= count;
        };
        ForwardList.prototype.splice_after = function (pos, from, first_before, last) {
            if (first_before === void 0) { first_before = null; }
            if (last === void 0) { last = null; }
            if (first_before == null)
                first_before = from.before_begin();
            else if (last == null)
                last = first_before.next().next();
            if (last == null)
                last = from.end();
            this.insert_after(pos, first_before.next(), last);
            from.erase_after(first_before, last);
        };
        ForwardList.prototype.merge = function (from, comp) {
            if (comp === void 0) { comp = std.less; }
            if (this == from)
                return;
            var it = this.before_begin();
            while (from.empty() == false) {
                var value = from.begin().value;
                while (!it.next().equals(this.end()) && comp(it.next().value, value))
                    it = it.next();
                this.splice_after(it, from, from.before_begin());
            }
        };
        ForwardList.prototype.sort = function (comp) {
            if (comp === void 0) { comp = std.less; }
            var vec = new std.Vector(this.begin(), this.end());
            std.sort(vec.begin(), vec.end(), comp);
            this.assign(vec.begin(), vec.end());
        };
        ForwardList.prototype.reverse = function () {
            var vec = new std.Vector(this.begin(), this.end());
            this.assign(vec.rbegin(), vec.rend());
        };
        ForwardList.prototype.swap = function (obj) {
            _a = __read([obj.size_, this.size_], 2), this.size_ = _a[0], obj.size_ = _a[1];
            _b = __read([obj.before_begin_, this.before_begin_], 2), this.before_begin_ = _b[0], obj.before_begin_ = _b[1];
            _c = __read([obj.before_begin_, this.before_begin_], 2), this.end_ = _c[0], obj.end_ = _c[1];
            _d = __read([obj.ptr_.value, this.ptr_.value], 2), this.ptr_.value = _d[0], obj.ptr_.value = _d[1];
            this.ptr_ = { value: this };
            obj.ptr_ = { value: obj };
            var _a, _b, _c, _d;
        };
        return ForwardList;
    }());
    std.ForwardList = ForwardList;
})(std || (std = {}));
(function (std) {
    var ForwardList;
    (function (ForwardList) {
        var Iterator = (function () {
            function Iterator(source, next, value) {
                this.source_ptr_ = source;
                this.next_ = next;
                this.value_ = value;
            }
            Iterator.prototype.source = function () {
                return this.source_ptr_.value;
            };
            Object.defineProperty(Iterator.prototype, "value", {
                get: function () {
                    return this.value_;
                },
                set: function (val) {
                    this.value_ = val;
                },
                enumerable: true,
                configurable: true
            });
            Iterator.prototype.next = function () {
                return this.next_;
            };
            Iterator.prototype.advance = function (n) {
                var ret = this;
                for (var i = 0; i < n; ++i)
                    ret = ret.next();
                return ret;
            };
            Iterator.prototype.equals = function (obj) {
                return this == obj;
            };
            return Iterator;
        }());
        ForwardList.Iterator = Iterator;
    })(ForwardList = std.ForwardList || (std.ForwardList = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var SetContainer = (function (_super) {
            __extends(SetContainer, _super);
            function SetContainer() {
                var _this = _super.call(this) || this;
                _this.data_ = new base._SetElementList(_this);
                return _this;
            }
            SetContainer.prototype.assign = function (begin, end) {
                this.clear();
                this.insert(begin, end);
            };
            SetContainer.prototype.clear = function () {
                this.data_.clear();
            };
            SetContainer.prototype.begin = function () {
                return this.data_.begin();
            };
            SetContainer.prototype.end = function () {
                return this.data_.end();
            };
            SetContainer.prototype.rbegin = function () {
                return this.data_.rbegin();
            };
            SetContainer.prototype.rend = function () {
                return this.data_.rend();
            };
            SetContainer.prototype.has = function (val) {
                return !this.find(val).equals(this.end());
            };
            SetContainer.prototype.size = function () {
                return this.data_.size();
            };
            SetContainer.prototype.push = function () {
                var items = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    items[_i] = arguments[_i];
                }
                if (items.length == 0)
                    return this.size();
                var first = new base._NativeArrayIterator(items, 0);
                var last = new base._NativeArrayIterator(items, items.length);
                this._Insert_by_range(first, last);
                return this.size();
            };
            SetContainer.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 1)
                    return this._Insert_by_val(args[0]);
                else if (args.length == 2) {
                    if (args[0].next instanceof Function && args[1].next instanceof Function) {
                        return this._Insert_by_range(args[0], args[1]);
                    }
                    else {
                        var ret = void 0;
                        var is_reverse_iterator = false;
                        if (args[0] instanceof base.SetReverseIterator) {
                            is_reverse_iterator = true;
                            args[0] = args[0].base().prev();
                        }
                        ret = this._Insert_by_hint(args[0], args[1]);
                        if (is_reverse_iterator == true)
                            return new base.SetReverseIterator(ret.next());
                        else
                            return ret;
                    }
                }
            };
            SetContainer.prototype.erase = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 1 && !(args[0] instanceof base.SetIterator && args[0].source() == this))
                    return this._Erase_by_val(args[0]);
                else if (args.length == 1)
                    return this._Erase_by_iterator(args[0]);
                else
                    return this._Erase_by_iterator(args[0], args[1]);
            };
            SetContainer.prototype._Erase_by_iterator = function (first, last) {
                if (last === void 0) { last = first.next(); }
                var ret;
                var is_reverse_iterator = false;
                if (first instanceof base.SetReverseIterator) {
                    is_reverse_iterator = true;
                    var first_it = last.base();
                    var last_it = first.base();
                    first = first_it;
                    last = last_it;
                }
                ret = this._Erase_by_range(first, last);
                if (is_reverse_iterator == true)
                    return new base.SetReverseIterator(ret.next());
                else
                    return ret;
            };
            SetContainer.prototype._Erase_by_val = function (val) {
                var it = this.find(val);
                if (it.equals(this.end()) == true)
                    return 0;
                this._Erase_by_iterator(it);
                return 1;
            };
            SetContainer.prototype._Erase_by_range = function (first, last) {
                var it = this.data_.erase(first, last);
                this._Handle_erase(first, last);
                return it;
            };
            SetContainer.prototype.swap = function (obj) {
                _a = __read([obj.data_, this.data_], 2), this.data_ = _a[0], obj.data_ = _a[1];
                _b = __read([obj.data_.associative_, this.data_.associative_], 2), this.data_.associative_ = _b[0], obj.data_.associative_ = _b[1];
                var _a, _b;
            };
            return SetContainer;
        }(base.Container));
        base.SetContainer = SetContainer;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var UniqueSet = (function (_super) {
            __extends(UniqueSet, _super);
            function UniqueSet() {
                return _super !== null && _super.apply(this, arguments) || this;
            }
            UniqueSet.prototype.count = function (key) {
                return this.find(key).equals(this.end()) ? 0 : 1;
            };
            UniqueSet.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                return _super.prototype.insert.apply(this, args);
            };
            UniqueSet.prototype.extract = function (param) {
                if (param instanceof base.SetIterator)
                    return this._Extract_by_iterator(param);
                else if (param instanceof base.SetReverseIterator)
                    return this._Extract_by_reverse_iterator(param);
                else
                    return this._Extract_by_key(param);
            };
            UniqueSet.prototype._Extract_by_key = function (val) {
                var it = this.find(val);
                if (it.equals(this.end()) == true)
                    throw new std.OutOfRange("No such key exists.");
                this.erase(val);
                return val;
            };
            UniqueSet.prototype._Extract_by_iterator = function (it) {
                if (it.equals(this.end()) == true || this.has(it.value) == false)
                    return this.end();
                this.erase(it);
                return it;
            };
            UniqueSet.prototype._Extract_by_reverse_iterator = function (it) {
                this._Extract_by_iterator(it.base().next());
                return it;
            };
            UniqueSet.prototype.merge = function (source) {
                for (var it = source.begin(); !it.equals(source.end());) {
                    if (this.has(it.value) == false) {
                        this.insert(it.value);
                        it = source.erase(it);
                    }
                    else
                        it = it.next();
                }
            };
            return UniqueSet;
        }(base.SetContainer));
        base.UniqueSet = UniqueSet;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var SetIterator = (function (_super) {
            __extends(SetIterator, _super);
            function SetIterator(source, prev, next, val) {
                var _this = _super.call(this, prev, next, val) || this;
                _this.source_ = source;
                return _this;
            }
            SetIterator.prototype.source = function () {
                return this.source_.associative();
            };
            SetIterator.prototype.prev = function () {
                return this.prev_;
            };
            SetIterator.prototype.next = function () {
                return this.next_;
            };
            SetIterator.prototype.advance = function (size) {
                return _super.prototype.advance.call(this, size);
            };
            SetIterator.prototype.less = function (obj) {
                return std.less(this.value, obj.value);
            };
            SetIterator.prototype.equals = function (obj) {
                return this == obj;
            };
            SetIterator.prototype.hashCode = function () {
                return std.hash(this.value);
            };
            SetIterator.prototype.swap = function (obj) {
                _super.prototype.swap.call(this, obj);
            };
            return SetIterator;
        }(base._ListIteratorBase));
        base.SetIterator = SetIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
(function (std) {
    var base;
    (function (base_3) {
        var SetReverseIterator = (function (_super) {
            __extends(SetReverseIterator, _super);
            function SetReverseIterator(base) {
                return _super.call(this, base) || this;
            }
            SetReverseIterator.prototype._Create_neighbor = function (base) {
                return new SetReverseIterator(base);
            };
            return SetReverseIterator;
        }(base_3.ReverseIterator));
        base_3.SetReverseIterator = SetReverseIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var TreeSet = (function (_super) {
        __extends(TreeSet, _super);
        function TreeSet() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var comp = std.less;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof TreeSet) {
                var container_1 = args[0];
                comp = container_1.key_comp();
                post_process = function () {
                    var first = container_1.begin();
                    var last = container_1.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length == 2)
                    comp = args[1];
                post_process = function () {
                    var items = args[0];
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length == 3)
                    comp = args[2];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else if (args.length == 1) {
                comp = args[0];
            }
            _this.tree_ = new std.base._UniqueSetTree(_this, comp);
            if (post_process != null)
                post_process();
            return _this;
        }
        TreeSet.prototype.clear = function () {
            _super.prototype.clear.call(this);
            this.tree_.clear();
        };
        TreeSet.prototype.find = function (val) {
            var node = this.tree_.nearest_by_key(val);
            if (node == null || this.tree_.key_eq()(node.value.value, val) == false)
                return this.end();
            else
                return node.value;
        };
        TreeSet.prototype.key_comp = function () {
            return this.tree_.key_comp();
        };
        TreeSet.prototype.value_comp = function () {
            return this.tree_.key_comp();
        };
        TreeSet.prototype.lower_bound = function (val) {
            return this.tree_.lower_bound(val);
        };
        TreeSet.prototype.upper_bound = function (val) {
            return this.tree_.upper_bound(val);
        };
        TreeSet.prototype.equal_range = function (val) {
            return this.tree_.equal_range(val);
        };
        TreeSet.prototype._Insert_by_val = function (val) {
            var it = this.lower_bound(val);
            if (!it.equals(this.end()) && this.tree_.key_eq()(it.value, val))
                return std.make_pair(it, false);
            it = this.data_.insert(it, val);
            this._Handle_insert(it, it.next());
            return std.make_pair(it, true);
        };
        TreeSet.prototype._Insert_by_hint = function (hint, val) {
            var prev = hint.prev();
            var keys = new std.Vector();
            if (!prev.equals(this.end()))
                if (this.tree_.key_eq()(prev.value, val))
                    return prev;
                else
                    keys.push_back(prev.value);
            keys.push_back(val);
            if (!hint.equals(this.end()))
                if (this.tree_.key_eq()(hint.value, val))
                    return hint;
                else
                    keys.push_back(hint.value);
            var ret;
            if (std.is_sorted(keys.begin(), keys.end(), this.key_comp())) {
                ret = this.data_.insert(hint, val);
                this._Handle_insert(ret, ret.next());
            }
            else
                ret = this._Insert_by_val(val).first;
            return ret;
        };
        TreeSet.prototype._Insert_by_range = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this._Insert_by_val(first.value);
        };
        TreeSet.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.insert(first);
        };
        TreeSet.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.erase(first);
        };
        TreeSet.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.tree_.source_, this.tree_.source_], 2), this.tree_.source_ = _a[0], obj.tree_.source_ = _a[1];
            _b = __read([obj.tree_, this.tree_], 2), this.tree_ = _b[0], obj.tree_ = _b[1];
            var _a, _b;
        };
        return TreeSet;
    }(std.base.UniqueSet));
    std.TreeSet = TreeSet;
})(std || (std = {}));
(function (std) {
    var TreeSet;
    (function (TreeSet) {
        TreeSet.Iterator = std.base.ArrayIterator;
        TreeSet.ReverseIterator = std.base.ArrayReverseIterator;
        TreeSet.iterator = TreeSet.Iterator;
        TreeSet.reverse_iterator = TreeSet.ReverseIterator;
    })(TreeSet = std.TreeSet || (std.TreeSet = {}));
})(std || (std = {}));
var std;
(function (std) {
    var HashSet = (function (_super) {
        __extends(HashSet, _super);
        function HashSet() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var hash_function = std.hash;
            var key_eq = std.equal_to;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof HashSet) {
                var container_2 = args[0];
                hash_function = container_2.hash_function();
                key_eq = container_2.key_eq();
                post_process = function () {
                    var first = container_2.begin();
                    var last = container_2.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length >= 2)
                    hash_function = args[1];
                if (args.length == 3)
                    key_eq = args[2];
                post_process = function () {
                    var items = args[0];
                    _this.reserve(items.length);
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length >= 3)
                    hash_function = args[2];
                if (args.length == 4)
                    key_eq = args[3];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else {
                if (args.length >= 1)
                    hash_function = args[0];
                if (args.length == 2)
                    key_eq = args[1];
            }
            _this.buckets_ = new std.base._SetHashBuckets(_this, hash_function, key_eq);
            if (post_process != null)
                post_process();
            return _this;
        }
        HashSet.prototype.clear = function () {
            this.buckets_.clear();
            _super.prototype.clear.call(this);
        };
        HashSet.prototype.find = function (key) {
            return this.buckets_.find(key);
        };
        HashSet.prototype.begin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.begin.call(this);
            else
                return this.buckets_.at(index).front();
        };
        HashSet.prototype.end = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.end.call(this);
            else
                return this.buckets_.at(index).back().next();
        };
        HashSet.prototype.rbegin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.rbegin.call(this);
            else
                return new std.base.SetReverseIterator(this.end(index));
        };
        HashSet.prototype.rend = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.rend.call(this);
            else
                return new std.base.SetReverseIterator(this.begin(index));
        };
        HashSet.prototype.bucket_count = function () {
            return this.buckets_.size();
        };
        HashSet.prototype.bucket_size = function (n) {
            return this.buckets_.at(n).size();
        };
        HashSet.prototype.load_factor = function () {
            return this.buckets_.load_factor();
        };
        HashSet.prototype.hash_function = function () {
            return this.buckets_.hash_function();
        };
        HashSet.prototype.key_eq = function () {
            return this.buckets_.key_eq();
        };
        HashSet.prototype.bucket = function (key) {
            return this.hash_function()(key) % this.buckets_.size();
        };
        HashSet.prototype.max_load_factor = function (z) {
            if (z === void 0) { z = null; }
            return this.buckets_.max_load_factor(z);
        };
        HashSet.prototype.reserve = function (n) {
            this.buckets_.rehash(Math.ceil(n * this.max_load_factor()));
        };
        HashSet.prototype.rehash = function (n) {
            if (n <= this.bucket_count())
                return;
            this.buckets_.rehash(n);
        };
        HashSet.prototype._Insert_by_val = function (val) {
            var it = this.find(val);
            if (it.equals(this.end()) == false)
                return std.make_pair(it, false);
            this.data_.push(val);
            it = it.prev();
            this._Handle_insert(it, it.next());
            return std.make_pair(it, true);
        };
        HashSet.prototype._Insert_by_hint = function (hint, val) {
            var it = this.find(val);
            if (it.equals(this.end()) == true) {
                it = this.data_.insert(hint, val);
                this._Handle_insert(it, it.next());
            }
            return it;
        };
        HashSet.prototype._Insert_by_range = function (first, last) {
            var my_first = this.end().prev();
            for (; !first.equals(last); first = first.next()) {
                if (this.has(first.value))
                    continue;
                this.data_.push(first.value);
            }
            my_first = my_first.next();
            if (this.size() > this.buckets_.capacity())
                this.reserve(Math.max(this.size(), this.buckets_.capacity() * 2));
            this._Handle_insert(my_first, this.end());
        };
        HashSet.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.insert(first);
        };
        HashSet.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.erase(first);
        };
        HashSet.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.buckets_.source_, this.buckets_.source_], 2), this.buckets_.source_ = _a[0], obj.buckets_.source_ = _a[1];
            _b = __read([obj.buckets_, this.buckets_], 2), this.buckets_ = _b[0], obj.buckets_ = _b[1];
            var _a, _b;
        };
        return HashSet;
    }(std.base.UniqueSet));
    std.HashSet = HashSet;
})(std || (std = {}));
(function (std) {
    var HashSet;
    (function (HashSet) {
        HashSet.Iterator = std.base.ArrayIterator;
        HashSet.ReverseIterator = std.base.ArrayReverseIterator;
        HashSet.iterator = HashSet.Iterator;
        HashSet.reverse_iterator = HashSet.ReverseIterator;
    })(HashSet = std.HashSet || (std.HashSet = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var MultiSet = (function (_super) {
            __extends(MultiSet, _super);
            function MultiSet() {
                return _super !== null && _super.apply(this, arguments) || this;
            }
            MultiSet.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                return _super.prototype.insert.apply(this, args);
            };
            MultiSet.prototype.merge = function (source) {
                this.insert(source.begin(), source.end());
                source.clear();
            };
            return MultiSet;
        }(base.SetContainer));
        base.MultiSet = MultiSet;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var TreeMultiSet = (function (_super) {
        __extends(TreeMultiSet, _super);
        function TreeMultiSet() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var comp = std.less;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof TreeMultiSet) {
                var container_3 = args[0];
                comp = container_3.key_comp();
                post_process = function () {
                    var first = container_3.begin();
                    var last = container_3.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length == 2)
                    comp = args[1];
                post_process = function () {
                    var items = args[0];
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length == 3)
                    comp = args[2];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else if (args.length == 1) {
                comp = args[0];
            }
            _this.tree_ = new std.base._MultiSetTree(_this, comp);
            if (post_process != null)
                post_process();
            return _this;
        }
        TreeMultiSet.prototype.clear = function () {
            _super.prototype.clear.call(this);
            this.tree_.clear();
        };
        TreeMultiSet.prototype.find = function (val) {
            var node = this.tree_.nearest_by_key(val);
            if (node == null || this.tree_.key_eq()(node.value.value, val) == false)
                return this.end();
            else
                return node.value;
        };
        TreeMultiSet.prototype.count = function (val) {
            var it = this.find(val);
            var cnt = 0;
            for (; !it.equals(this.end()) && this.tree_.key_eq()(it.value, val); it = it.next())
                cnt++;
            return cnt;
        };
        TreeMultiSet.prototype.key_comp = function () {
            return this.tree_.key_comp();
        };
        TreeMultiSet.prototype.value_comp = function () {
            return this.tree_.key_comp();
        };
        TreeMultiSet.prototype.lower_bound = function (val) {
            return this.tree_.lower_bound(val);
        };
        TreeMultiSet.prototype.upper_bound = function (val) {
            return this.tree_.upper_bound(val);
        };
        TreeMultiSet.prototype.equal_range = function (val) {
            return this.tree_.equal_range(val);
        };
        TreeMultiSet.prototype._Insert_by_val = function (val) {
            var it = this.upper_bound(val);
            it = this.data_.insert(it, val);
            this._Handle_insert(it, it.next());
            return it;
        };
        TreeMultiSet.prototype._Insert_by_hint = function (hint, val) {
            var prev = hint.prev();
            var keys = new std.Vector();
            if (!prev.equals(this.end()) && !this.tree_.key_eq()(prev.value, val))
                keys.push_back(prev.value);
            keys.push_back(val);
            if (!hint.equals(this.end()) && !this.tree_.key_eq()(hint.value, val))
                keys.push_back(hint.value);
            var ret;
            if (std.is_sorted(keys.begin(), keys.end(), this.key_comp())) {
                ret = this.data_.insert(hint, val);
                this._Handle_insert(ret, ret.next());
            }
            else
                ret = this._Insert_by_val(val);
            return ret;
        };
        TreeMultiSet.prototype._Insert_by_range = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this._Insert_by_val(first.value);
        };
        TreeMultiSet.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.insert(first);
        };
        TreeMultiSet.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.erase(first);
        };
        TreeMultiSet.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.tree_.source_, this.tree_.source_], 2), this.tree_.source_ = _a[0], obj.tree_.source_ = _a[1];
            _b = __read([obj.tree_, this.tree_], 2), this.tree_ = _b[0], obj.tree_ = _b[1];
            var _a, _b;
        };
        return TreeMultiSet;
    }(std.base.MultiSet));
    std.TreeMultiSet = TreeMultiSet;
})(std || (std = {}));
(function (std) {
    var TreeMultiSet;
    (function (TreeMultiSet) {
        TreeMultiSet.Iterator = std.base.ArrayIterator;
        TreeMultiSet.ReverseIterator = std.base.ArrayReverseIterator;
        TreeMultiSet.iterator = TreeMultiSet.Iterator;
        TreeMultiSet.reverse_iterator = TreeMultiSet.ReverseIterator;
    })(TreeMultiSet = std.TreeMultiSet || (std.TreeMultiSet = {}));
})(std || (std = {}));
var std;
(function (std) {
    var HashMultiSet = (function (_super) {
        __extends(HashMultiSet, _super);
        function HashMultiSet() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var hash_function = std.hash;
            var key_eq = std.equal_to;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof HashMultiSet) {
                var container_4 = args[0];
                hash_function = container_4.hash_function();
                key_eq = container_4.key_eq();
                post_process = function () {
                    var first = container_4.begin();
                    var last = container_4.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length >= 2)
                    hash_function = args[1];
                if (args.length == 3)
                    key_eq = args[2];
                post_process = function () {
                    var items = args[0];
                    _this.reserve(items.length);
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length >= 3)
                    hash_function = args[2];
                if (args.length == 4)
                    key_eq = args[3];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else {
                if (args.length >= 1)
                    hash_function = args[0];
                if (args.length == 2)
                    key_eq = args[1];
            }
            _this.buckets_ = new std.base._SetHashBuckets(_this, hash_function, key_eq);
            if (post_process != null)
                post_process();
            return _this;
        }
        HashMultiSet.prototype.clear = function () {
            this.buckets_.clear();
            _super.prototype.clear.call(this);
        };
        HashMultiSet.prototype.find = function (key) {
            return this.buckets_.find(key);
        };
        HashMultiSet.prototype.count = function (key) {
            var index = this.bucket(key);
            var bucket = this.buckets_.at(index);
            var cnt = 0;
            try {
                for (var bucket_1 = __values(bucket), bucket_1_1 = bucket_1.next(); !bucket_1_1.done; bucket_1_1 = bucket_1.next()) {
                    var it = bucket_1_1.value;
                    if (this.buckets_.key_eq()(it.value, key))
                        ++cnt;
                }
            }
            catch (e_2_1) { e_2 = { error: e_2_1 }; }
            finally {
                try {
                    if (bucket_1_1 && !bucket_1_1.done && (_a = bucket_1.return)) _a.call(bucket_1);
                }
                finally { if (e_2) throw e_2.error; }
            }
            return cnt;
            var e_2, _a;
        };
        HashMultiSet.prototype.begin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.begin.call(this);
            else
                return this.buckets_.at(index).front();
        };
        HashMultiSet.prototype.end = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.end.call(this);
            else
                return this.buckets_.at(index).back().next();
        };
        HashMultiSet.prototype.rbegin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.rbegin.call(this);
            else
                return new std.base.SetReverseIterator(this.end(index));
        };
        HashMultiSet.prototype.rend = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.rend.call(this);
            else
                return new std.base.SetReverseIterator(this.begin(index));
        };
        HashMultiSet.prototype.bucket_count = function () {
            return this.buckets_.size();
        };
        HashMultiSet.prototype.bucket_size = function (n) {
            return this.buckets_.at(n).size();
        };
        HashMultiSet.prototype.load_factor = function () {
            return this.buckets_.load_factor();
        };
        HashMultiSet.prototype.hash_function = function () {
            return this.buckets_.hash_function();
        };
        HashMultiSet.prototype.key_eq = function () {
            return this.buckets_.key_eq();
        };
        HashMultiSet.prototype.bucket = function (key) {
            return this.hash_function()(key) % this.buckets_.size();
        };
        HashMultiSet.prototype.max_load_factor = function (z) {
            if (z === void 0) { z = null; }
            return this.buckets_.max_load_factor(z);
        };
        HashMultiSet.prototype.reserve = function (n) {
            this.buckets_.rehash(Math.ceil(n * this.max_load_factor()));
        };
        HashMultiSet.prototype.rehash = function (n) {
            if (n <= this.bucket_count())
                return;
            this.buckets_.rehash(n);
        };
        HashMultiSet.prototype._Insert_by_val = function (val) {
            var it = this.data_.insert(this.data_.end(), val);
            this._Handle_insert(it, it.next());
            return it;
        };
        HashMultiSet.prototype._Insert_by_hint = function (hint, val) {
            var it = this.data_.insert(hint, val);
            this._Handle_insert(it, it.next());
            return it;
        };
        HashMultiSet.prototype._Insert_by_range = function (first, last) {
            var my_first = this.data_.insert(this.data_.end(), first, last);
            if (this.size() > this.buckets_.capacity())
                this.reserve(Math.max(this.size(), this.buckets_.capacity() * 2));
            this._Handle_insert(my_first, this.end());
        };
        HashMultiSet.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.insert(first);
        };
        HashMultiSet.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.erase(first);
        };
        HashMultiSet.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.buckets_.source_, this.buckets_.source_], 2), this.buckets_.source_ = _a[0], obj.buckets_.source_ = _a[1];
            _b = __read([obj.buckets_, this.buckets_], 2), this.buckets_ = _b[0], obj.buckets_ = _b[1];
            var _a, _b;
        };
        return HashMultiSet;
    }(std.base.MultiSet));
    std.HashMultiSet = HashMultiSet;
})(std || (std = {}));
(function (std) {
    var HashMultiSet;
    (function (HashMultiSet) {
        HashMultiSet.Iterator = std.base.ArrayIterator;
        HashMultiSet.ReverseIterator = std.base.ArrayReverseIterator;
        HashMultiSet.iterator = HashMultiSet.Iterator;
        HashMultiSet.reverse_iterator = HashMultiSet.ReverseIterator;
    })(HashMultiSet = std.HashMultiSet || (std.HashMultiSet = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var MapContainer = (function (_super) {
            __extends(MapContainer, _super);
            function MapContainer() {
                var _this = _super.call(this) || this;
                _this.data_ = new base._MapElementList(_this);
                return _this;
            }
            MapContainer.prototype.assign = function (first, last) {
                this.clear();
                this.insert(first, last);
            };
            MapContainer.prototype.clear = function () {
                this.data_.clear();
            };
            MapContainer.prototype.begin = function () {
                return this.data_.begin();
            };
            MapContainer.prototype.end = function () {
                return this.data_.end();
            };
            MapContainer.prototype.rbegin = function () {
                return this.data_.rbegin();
            };
            MapContainer.prototype.rend = function () {
                return this.data_.rend();
            };
            MapContainer.prototype.has = function (key) {
                return !this.find(key).equals(this.end());
            };
            MapContainer.prototype.size = function () {
                return this.data_.size();
            };
            MapContainer.prototype.push = function () {
                var items = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    items[_i] = arguments[_i];
                }
                var first = new base._NativeArrayIterator(items, 0);
                var last = new base._NativeArrayIterator(items, items.length);
                this.insert(first, last);
                return this.size();
            };
            MapContainer.prototype.emplace_hint = function (hint) {
                var args = [];
                for (var _i = 1; _i < arguments.length; _i++) {
                    args[_i - 1] = arguments[_i];
                }
                if (args.length == 1)
                    return this._Emplace_hint(hint, args[0].first, args[0].second);
                else
                    return this._Emplace_hint(hint, args[0], args[1]);
            };
            MapContainer.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 1) {
                    return this._Emplace(args[0].first, args[0].second);
                }
                else if (args.length == 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                    return this._Insert_by_range(args[0], args[1]);
                }
                else {
                    var ret = void 0;
                    var is_reverse_iterator = false;
                    if (args[0] instanceof base.MapReverseIterator) {
                        is_reverse_iterator = true;
                        args[0] = args[0].base().prev();
                    }
                    ret = this._Emplace_hint(args[0], args[1].first, args[1].second);
                    if (is_reverse_iterator == true)
                        return new base.MapReverseIterator(ret.next());
                    else
                        return ret;
                }
            };
            MapContainer.prototype.erase = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 1 && (args[0] instanceof base.Iterator == false || args[0].source() != this))
                    return this._Erase_by_key(args[0]);
                else if (args.length == 1)
                    return this._Erase_by_iterator(args[0]);
                else
                    return this._Erase_by_iterator(args[0], args[1]);
            };
            MapContainer.prototype._Erase_by_key = function (key) {
                var it = this.find(key);
                if (it.equals(this.end()) == true)
                    return 0;
                this._Erase_by_iterator(it);
                return 1;
            };
            MapContainer.prototype._Erase_by_iterator = function (first, last) {
                if (last === void 0) { last = first.next(); }
                var ret;
                var is_reverse_iterator = false;
                if (first instanceof base.MapReverseIterator) {
                    is_reverse_iterator = true;
                    var first_it = last.base();
                    var last_it = first.base();
                    first = first_it;
                    last = last_it;
                }
                ret = this._Erase_by_range(first, last);
                if (is_reverse_iterator == true)
                    return new base.MapReverseIterator(ret.next());
                else
                    return ret;
            };
            MapContainer.prototype._Erase_by_range = function (first, last) {
                var it = this.data_.erase(first, last);
                this._Handle_erase(first, last);
                return it;
            };
            MapContainer.prototype.swap = function (obj) {
                _a = __read([obj.data_, this.data_], 2), this.data_ = _a[0], obj.data_ = _a[1];
                _b = __read([obj.data_.associative_, this.data_.associative_], 2), this.data_.associative_ = _b[0], obj.data_.associative_ = _b[1];
                var _a, _b;
            };
            return MapContainer;
        }(base.Container));
        base.MapContainer = MapContainer;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var UniqueMap = (function (_super) {
            __extends(UniqueMap, _super);
            function UniqueMap() {
                return _super !== null && _super.apply(this, arguments) || this;
            }
            UniqueMap.prototype.count = function (key) {
                return this.find(key).equals(this.end()) ? 0 : 1;
            };
            UniqueMap.prototype.get = function (key) {
                var it = this.find(key);
                if (it.equals(this.end()) == true)
                    throw new std.OutOfRange("unable to find the matched key.");
                return it.second;
            };
            UniqueMap.prototype.set = function (key, val) {
                this.insert_or_assign(key, val);
            };
            UniqueMap.prototype.emplace = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 1)
                    return this._Emplace(args[0].first, args[0].second);
                else
                    return this._Emplace(args[0], args[1]);
            };
            UniqueMap.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                return _super.prototype.insert.apply(this, args);
            };
            UniqueMap.prototype.insert_or_assign = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 2) {
                    return this._Insert_or_assign_with_key_value(args[0], args[1]);
                }
                else if (args.length == 3) {
                    var ret = void 0;
                    var is_reverse_iterator = false;
                    if (args[0] instanceof base.MapReverseIterator) {
                        is_reverse_iterator = true;
                        args[0] = args[0].base().prev();
                    }
                    ret = this._Insert_or_assign_with_hint(args[0], args[1], args[2]);
                    if (is_reverse_iterator == true)
                        return new base.MapReverseIterator(ret.next());
                    else
                        return ret;
                }
            };
            UniqueMap.prototype._Insert_or_assign_with_key_value = function (key, value) {
                var it = this.find(key);
                if (it.equals(this.end()) == true)
                    return this._Emplace(key, value);
                else {
                    it.second = value;
                    return std.make_pair(it, false);
                }
            };
            UniqueMap.prototype._Insert_or_assign_with_hint = function (hint, key, value) {
                var it = this._Emplace_hint(hint, key, value);
                if (it.second != value)
                    it.second = value;
                return it;
            };
            UniqueMap.prototype.extract = function (param) {
                if (param instanceof base.MapIterator)
                    return this._Extract_by_iterator(param);
                else if (param instanceof base.MapReverseIterator)
                    return this._Extract_by_reverse_iterator(param);
                else
                    return this._Extract_by_key(param);
            };
            UniqueMap.prototype._Extract_by_key = function (key) {
                var it = this.find(key);
                if (it.equals(this.end()) == true)
                    throw new std.OutOfRange("No such key exists.");
                var ret = it.value;
                this.erase(it);
                return ret;
            };
            UniqueMap.prototype._Extract_by_iterator = function (it) {
                if (it.equals(this.end()) == true)
                    return this.end();
                this.erase(it);
                return it;
            };
            UniqueMap.prototype._Extract_by_reverse_iterator = function (it) {
                this._Extract_by_iterator(it.base().next());
                return it;
            };
            UniqueMap.prototype.merge = function (source) {
                for (var it = source.begin(); !it.equals(source.end());) {
                    if (this.has(it.first) == false) {
                        this.insert(it.value);
                        it = source.erase(it);
                    }
                    else
                        it = it.next();
                }
            };
            return UniqueMap;
        }(base.MapContainer));
        base.UniqueMap = UniqueMap;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var MapIterator = (function (_super) {
            __extends(MapIterator, _super);
            function MapIterator(associative, prev, next, val) {
                var _this = _super.call(this, prev, next, val) || this;
                _this.source_ = associative;
                return _this;
            }
            MapIterator.prototype.prev = function () {
                return this.prev_;
            };
            MapIterator.prototype.next = function () {
                return this.next_;
            };
            MapIterator.prototype.advance = function (step) {
                return _super.prototype.advance.call(this, step);
            };
            MapIterator.prototype.source = function () {
                return this.source_.associative();
            };
            Object.defineProperty(MapIterator.prototype, "first", {
                get: function () {
                    return this.value.first;
                },
                enumerable: true,
                configurable: true
            });
            Object.defineProperty(MapIterator.prototype, "second", {
                get: function () {
                    return this.value.second;
                },
                set: function (val) {
                    this.value.second = val;
                },
                enumerable: true,
                configurable: true
            });
            MapIterator.prototype.less = function (obj) {
                return std.less(this.first, obj.first);
            };
            MapIterator.prototype.equals = function (obj) {
                return this == obj;
            };
            MapIterator.prototype.hashCode = function () {
                return std.hash(this.first);
            };
            MapIterator.prototype.swap = function (obj) {
                _super.prototype.swap.call(this, obj);
            };
            return MapIterator;
        }(base._ListIteratorBase));
        base.MapIterator = MapIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
(function (std) {
    var base;
    (function (base_4) {
        var MapReverseIterator = (function (_super) {
            __extends(MapReverseIterator, _super);
            function MapReverseIterator(base) {
                return _super.call(this, base) || this;
            }
            MapReverseIterator.prototype._Create_neighbor = function (base) {
                return new MapReverseIterator(base);
            };
            Object.defineProperty(MapReverseIterator.prototype, "first", {
                get: function () {
                    return this.base_.first;
                },
                enumerable: true,
                configurable: true
            });
            Object.defineProperty(MapReverseIterator.prototype, "second", {
                get: function () {
                    return this.base_.second;
                },
                set: function (val) {
                    this.base_.second = val;
                },
                enumerable: true,
                configurable: true
            });
            return MapReverseIterator;
        }(base.ReverseIterator));
        base_4.MapReverseIterator = MapReverseIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var TreeMap = (function (_super) {
        __extends(TreeMap, _super);
        function TreeMap() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var comp = std.less;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof TreeMap) {
                var container_5 = args[0];
                comp = container_5.key_comp();
                post_process = function () {
                    var first = container_5.begin();
                    var last = container_5.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length == 2)
                    comp = args[1];
                post_process = function () {
                    var items = args[0];
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length == 3)
                    comp = args[2];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else if (args.length == 1) {
                comp = args[0];
            }
            _this.tree_ = new std.base._UniqueMapTree(_this, comp);
            if (post_process != null)
                post_process();
            return _this;
        }
        TreeMap.prototype.clear = function () {
            _super.prototype.clear.call(this);
            this.tree_.clear();
        };
        TreeMap.prototype.find = function (key) {
            var node = this.tree_.nearest_by_key(key);
            if (node == null || this.tree_.key_eq()(node.value.first, key) == false)
                return this.end();
            else
                return node.value;
        };
        TreeMap.prototype.key_comp = function () {
            return this.tree_.key_comp();
        };
        TreeMap.prototype.value_comp = function () {
            return this.tree_.value_comp();
        };
        TreeMap.prototype.lower_bound = function (key) {
            return this.tree_.lower_bound(key);
        };
        TreeMap.prototype.upper_bound = function (key) {
            return this.tree_.upper_bound(key);
        };
        TreeMap.prototype.equal_range = function (key) {
            return this.tree_.equal_range(key);
        };
        TreeMap.prototype._Emplace = function (key, val) {
            var it = this.lower_bound(key);
            if (!it.equals(this.end()) && this.tree_.key_eq()(it.first, key))
                return std.make_pair(it, false);
            it = this.data_.insert(it, new std.Entry(key, val));
            this._Handle_insert(it, it.next());
            return std.make_pair(it, true);
        };
        TreeMap.prototype._Emplace_hint = function (hint, key, val) {
            var prev = hint.prev();
            var keys = new std.Vector();
            if (!prev.equals(this.end()))
                if (this.tree_.key_eq()(prev.first, key))
                    return prev;
                else
                    keys.push_back(prev.first);
            keys.push_back(key);
            if (!hint.equals(this.end()))
                if (this.tree_.key_eq()(hint.first, key))
                    return hint;
                else
                    keys.push_back(hint.first);
            var ret;
            if (std.is_sorted(keys.begin(), keys.end(), this.key_comp())) {
                ret = this.data_.insert(hint, new std.Entry(key, val));
                this._Handle_insert(ret, ret.next());
            }
            else
                ret = this._Emplace(key, val).first;
            return ret;
        };
        TreeMap.prototype._Insert_by_range = function (first, last) {
            for (var it = first; !it.equals(last); it = it.next())
                this._Emplace(it.value.first, it.value.second);
        };
        TreeMap.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.insert(first);
        };
        TreeMap.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.erase(first);
        };
        TreeMap.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.tree_.source_, this.tree_.source_], 2), this.tree_.source_ = _a[0], obj.tree_.source_ = _a[1];
            _b = __read([obj.tree_, this.tree_], 2), this.tree_ = _b[0], obj.tree_ = _b[1];
            var _a, _b;
        };
        return TreeMap;
    }(std.base.UniqueMap));
    std.TreeMap = TreeMap;
})(std || (std = {}));
(function (std) {
    var TreeMap;
    (function (TreeMap) {
        TreeMap.Iterator = std.base.MapIterator;
        TreeMap.ReverseIterator = std.base.MapReverseIterator;
        TreeMap.iterator = TreeMap.Iterator;
        TreeMap.reverse_iterator = TreeMap.ReverseIterator;
    })(TreeMap = std.TreeMap || (std.TreeMap = {}));
})(std || (std = {}));
var std;
(function (std) {
    var HashMap = (function (_super) {
        __extends(HashMap, _super);
        function HashMap() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var hash_function = std.hash;
            var key_eq = std.equal_to;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof HashMap) {
                var container_6 = args[0];
                hash_function = container_6.hash_function();
                key_eq = container_6.key_eq();
                post_process = function () {
                    var first = container_6.begin();
                    var last = container_6.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length >= 2)
                    hash_function = args[1];
                if (args.length == 3)
                    key_eq = args[2];
                post_process = function () {
                    var items = args[0];
                    _this.reserve(items.length);
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length >= 3)
                    hash_function = args[2];
                if (args.length == 4)
                    key_eq = args[3];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else {
                if (args.length >= 1)
                    hash_function = args[0];
                if (args.length == 2)
                    key_eq = args[1];
            }
            _this.buckets_ = new std.base._MapHashBuckets(_this, hash_function, key_eq);
            if (post_process != null)
                post_process();
            return _this;
        }
        HashMap.prototype.clear = function () {
            this.buckets_.clear();
            _super.prototype.clear.call(this);
        };
        HashMap.prototype.find = function (key) {
            return this.buckets_.find(key);
        };
        HashMap.prototype.begin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.begin.call(this);
            else
                return this.buckets_.at(index).front();
        };
        HashMap.prototype.end = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.end.call(this);
            else
                return this.buckets_.at(index).back().next();
        };
        HashMap.prototype.rbegin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.rbegin.call(this);
            else
                return new std.base.MapReverseIterator(this.end(index));
        };
        HashMap.prototype.rend = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.rend.call(this);
            else
                return new std.base.MapReverseIterator(this.begin(index));
        };
        HashMap.prototype.bucket_count = function () {
            return this.buckets_.size();
        };
        HashMap.prototype.bucket_size = function (index) {
            return this.buckets_.at(index).size();
        };
        HashMap.prototype.load_factor = function () {
            return this.buckets_.load_factor();
        };
        HashMap.prototype.hash_function = function () {
            return this.buckets_.hash_function();
        };
        HashMap.prototype.key_eq = function () {
            return this.buckets_.key_eq();
        };
        HashMap.prototype.bucket = function (key) {
            return this.hash_function()(key) % this.buckets_.size();
        };
        HashMap.prototype.max_load_factor = function (z) {
            if (z === void 0) { z = null; }
            return this.buckets_.max_load_factor(z);
        };
        HashMap.prototype.reserve = function (n) {
            this.buckets_.reserve(n);
        };
        HashMap.prototype.rehash = function (n) {
            if (n <= this.bucket_count())
                return;
            this.buckets_.rehash(n);
        };
        HashMap.prototype._Emplace = function (key, val) {
            var it = this.find(key);
            if (it.equals(this.end()) == false)
                return std.make_pair(it, false);
            this.data_.push(new std.Entry(key, val));
            it = it.prev();
            this._Handle_insert(it, it.next());
            return std.make_pair(it, true);
        };
        HashMap.prototype._Emplace_hint = function (hint, key, val) {
            var it = this.find(key);
            if (it.equals(this.end()) == true) {
                it = this.data_.insert(hint, new std.Entry(key, val));
                this._Handle_insert(it, it.next());
            }
            return it;
        };
        HashMap.prototype._Insert_by_range = function (first, last) {
            var my_first = this.end().prev();
            for (var it = first; !it.equals(last); it = it.next()) {
                if (this.has(it.value.first))
                    continue;
                this.data_.push(new std.Entry(it.value.first, it.value.second));
            }
            my_first = my_first.next();
            if (this.size() > this.buckets_.capacity())
                this.reserve(Math.max(this.size(), this.buckets_.capacity() * 2));
            this._Handle_insert(my_first, this.end());
        };
        HashMap.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.insert(first);
        };
        HashMap.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.erase(first);
        };
        HashMap.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.buckets_.source_, this.buckets_.source_], 2), this.buckets_.source_ = _a[0], obj.buckets_.source_ = _a[1];
            _b = __read([obj.buckets_, this.buckets_], 2), this.buckets_ = _b[0], obj.buckets_ = _b[1];
            var _a, _b;
        };
        return HashMap;
    }(std.base.UniqueMap));
    std.HashMap = HashMap;
})(std || (std = {}));
(function (std) {
    var HashMap;
    (function (HashMap) {
        HashMap.Iterator = std.base.MapIterator;
        HashMap.ReverseIterator = std.base.MapReverseIterator;
        HashMap.iterator = HashMap.Iterator;
        HashMap.reverse_iterator = HashMap.ReverseIterator;
    })(HashMap = std.HashMap || (std.HashMap = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var MultiMap = (function (_super) {
            __extends(MultiMap, _super);
            function MultiMap() {
                return _super !== null && _super.apply(this, arguments) || this;
            }
            MultiMap.prototype.emplace = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                if (args.length == 1)
                    return this._Emplace(args[0].first, args[0].second);
                else
                    return this._Emplace(args[0], args[1]);
            };
            MultiMap.prototype.insert = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                return _super.prototype.insert.apply(this, args);
            };
            MultiMap.prototype.merge = function (source) {
                this.insert(source.begin(), source.end());
                source.clear();
            };
            return MultiMap;
        }(base.MapContainer));
        base.MultiMap = MultiMap;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var TreeMultiMap = (function (_super) {
        __extends(TreeMultiMap, _super);
        function TreeMultiMap() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var comp = std.less;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof TreeMultiMap) {
                var container_7 = args[0];
                comp = container_7.key_comp();
                post_process = function () {
                    var first = container_7.begin();
                    var last = container_7.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length == 2)
                    comp = args[1];
                post_process = function () {
                    var items = args[0];
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length == 3)
                    comp = args[2];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else if (args.length == 1) {
                comp = args[0];
            }
            _this.tree_ = new std.base._MultiMapTree(_this, comp);
            if (post_process != null)
                post_process();
            return _this;
        }
        TreeMultiMap.prototype.clear = function () {
            _super.prototype.clear.call(this);
            this.tree_.clear();
        };
        TreeMultiMap.prototype.find = function (key) {
            var node = this.tree_.nearest_by_key(key);
            if (node == null || this.tree_.key_eq()(node.value.first, key) == false)
                return this.end();
            else
                return node.value;
        };
        TreeMultiMap.prototype.count = function (key) {
            var it = this.find(key);
            var cnt = 0;
            for (; !it.equals(this.end()) && this.tree_.key_eq()(it.first, key); it = it.next())
                cnt++;
            return cnt;
        };
        TreeMultiMap.prototype.key_comp = function () {
            return this.tree_.key_comp();
        };
        TreeMultiMap.prototype.value_comp = function () {
            return this.tree_.value_comp();
        };
        TreeMultiMap.prototype.lower_bound = function (key) {
            return this.tree_.lower_bound(key);
        };
        TreeMultiMap.prototype.upper_bound = function (key) {
            return this.tree_.upper_bound(key);
        };
        TreeMultiMap.prototype.equal_range = function (key) {
            return this.tree_.equal_range(key);
        };
        TreeMultiMap.prototype._Emplace = function (key, val) {
            var it = this.upper_bound(key);
            it = this.data_.insert(it, new std.Entry(key, val));
            this._Handle_insert(it, it.next());
            return it;
        };
        TreeMultiMap.prototype._Emplace_hint = function (hint, key, val) {
            var prev = hint.prev();
            var keys = new std.Vector();
            if (!prev.equals(this.end()) && !this.tree_.key_eq()(prev.first, key))
                keys.push_back(prev.first);
            keys.push_back(key);
            if (!hint.equals(this.end()) && !this.tree_.key_eq()(hint.first, key))
                keys.push_back(hint.first);
            var ret;
            if (std.is_sorted(keys.begin(), keys.end(), this.key_comp())) {
                ret = this.data_.insert(hint, new std.Entry(key, val));
                this._Handle_insert(ret, ret.next());
            }
            else
                ret = this._Emplace(key, val);
            return ret;
        };
        TreeMultiMap.prototype._Insert_by_range = function (first, last) {
            for (var it = first; !it.equals(last); it = it.next())
                this._Emplace(it.value.first, it.value.second);
        };
        TreeMultiMap.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.insert(first);
        };
        TreeMultiMap.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.tree_.erase(first);
        };
        TreeMultiMap.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.tree_.source_, this.tree_.source_], 2), this.tree_.source_ = _a[0], obj.tree_.source_ = _a[1];
            _b = __read([obj.tree_, this.tree_], 2), this.tree_ = _b[0], obj.tree_ = _b[1];
            var _a, _b;
        };
        return TreeMultiMap;
    }(std.base.MultiMap));
    std.TreeMultiMap = TreeMultiMap;
})(std || (std = {}));
(function (std) {
    var TreeMultiMap;
    (function (TreeMultiMap) {
        TreeMultiMap.Iterator = std.base.MapIterator;
        TreeMultiMap.ReverseIterator = std.base.MapReverseIterator;
        TreeMultiMap.iterator = TreeMultiMap.Iterator;
        TreeMultiMap.reverse_iterator = TreeMultiMap.ReverseIterator;
    })(TreeMultiMap = std.TreeMultiMap || (std.TreeMultiMap = {}));
})(std || (std = {}));
var std;
(function (std) {
    var HashMultiMap = (function (_super) {
        __extends(HashMultiMap, _super);
        function HashMultiMap() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this) || this;
            var hash_function = std.hash;
            var key_eq = std.equal_to;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof HashMultiMap) {
                var container_8 = args[0];
                hash_function = container_8.hash_function();
                key_eq = container_8.key_eq();
                post_process = function () {
                    var first = container_8.begin();
                    var last = container_8.end();
                    _this.assign(first, last);
                };
            }
            else if (args.length >= 1 && args[0] instanceof Array) {
                if (args.length >= 2)
                    hash_function = args[1];
                if (args.length == 3)
                    key_eq = args[2];
                post_process = function () {
                    var items = args[0];
                    _this.reserve(items.length);
                    _this.push.apply(_this, __spread(items));
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length >= 3)
                    hash_function = args[2];
                if (args.length == 4)
                    key_eq = args[3];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.assign(first, last);
                };
            }
            else {
                if (args.length >= 1)
                    hash_function = args[0];
                if (args.length == 2)
                    key_eq = args[1];
            }
            _this.buckets_ = new std.base._MapHashBuckets(_this, hash_function, key_eq);
            if (post_process != null)
                post_process();
            return _this;
        }
        HashMultiMap.prototype.clear = function () {
            this.buckets_.clear();
            _super.prototype.clear.call(this);
        };
        HashMultiMap.prototype.find = function (key) {
            return this.buckets_.find(key);
        };
        HashMultiMap.prototype.count = function (key) {
            var index = this.bucket(key);
            var bucket = this.buckets_.at(index);
            var cnt = 0;
            try {
                for (var bucket_2 = __values(bucket), bucket_2_1 = bucket_2.next(); !bucket_2_1.done; bucket_2_1 = bucket_2.next()) {
                    var it = bucket_2_1.value;
                    if (this.buckets_.key_eq()(it.first, key))
                        ++cnt;
                }
            }
            catch (e_3_1) { e_3 = { error: e_3_1 }; }
            finally {
                try {
                    if (bucket_2_1 && !bucket_2_1.done && (_a = bucket_2.return)) _a.call(bucket_2);
                }
                finally { if (e_3) throw e_3.error; }
            }
            return cnt;
            var e_3, _a;
        };
        HashMultiMap.prototype.begin = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.begin.call(this);
            else
                return this.buckets_.at(index).front();
        };
        HashMultiMap.prototype.end = function (index) {
            if (index === void 0) { index = -1; }
            if (index == -1)
                return _super.prototype.end.call(this);
            else
                return this.buckets_.at(index).back().next();
        };
        HashMultiMap.prototype.rbegin = function (index) {
            if (index === void 0) { index = -1; }
            return new std.base.MapReverseIterator(this.end(index));
        };
        HashMultiMap.prototype.rend = function (index) {
            if (index === void 0) { index = -1; }
            return new std.base.MapReverseIterator(this.begin(index));
        };
        HashMultiMap.prototype.bucket_count = function () {
            return this.buckets_.size();
        };
        HashMultiMap.prototype.bucket_size = function (index) {
            return this.buckets_.at(index).size();
        };
        HashMultiMap.prototype.load_factor = function () {
            return this.buckets_.load_factor();
        };
        HashMultiMap.prototype.hash_function = function () {
            return this.buckets_.hash_function();
        };
        HashMultiMap.prototype.key_eq = function () {
            return this.buckets_.key_eq();
        };
        HashMultiMap.prototype.bucket = function (key) {
            return this.hash_function()(key) % this.buckets_.size();
        };
        HashMultiMap.prototype.max_load_factor = function (z) {
            if (z === void 0) { z = null; }
            return this.buckets_.max_load_factor(z);
        };
        HashMultiMap.prototype.reserve = function (n) {
            this.buckets_.reserve(n);
        };
        HashMultiMap.prototype.rehash = function (n) {
            if (n <= this.bucket_count())
                return;
            this.buckets_.rehash(n);
        };
        HashMultiMap.prototype._Emplace = function (key, val) {
            var it = this.data_.insert(this.data_.end(), new std.Entry(key, val));
            this._Handle_insert(it, it.next());
            return it;
        };
        HashMultiMap.prototype._Emplace_hint = function (hint, key, val) {
            var it = this.data_.insert(hint, new std.Entry(key, val));
            this._Handle_insert(it, it.next());
            return it;
        };
        HashMultiMap.prototype._Insert_by_range = function (first, last) {
            var entries = [];
            for (var it = first; !it.equals(last); it = it.next())
                entries.push(new std.Entry(it.value.first, it.value.second));
            var my_first = this.data_.insert(this.data_.end(), new std.base._NativeArrayIterator(entries, 0), new std.base._NativeArrayIterator(entries, entries.length));
            if (this.size() > this.buckets_.capacity())
                this.reserve(Math.max(this.size(), this.buckets_.capacity() * 2));
            this._Handle_insert(my_first, this.end());
        };
        HashMultiMap.prototype._Handle_insert = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.insert(first);
        };
        HashMultiMap.prototype._Handle_erase = function (first, last) {
            for (; !first.equals(last); first = first.next())
                this.buckets_.erase(first);
        };
        HashMultiMap.prototype.swap = function (obj) {
            _super.prototype.swap.call(this, obj);
            _a = __read([obj.buckets_.source_, this.buckets_.source_], 2), this.buckets_.source_ = _a[0], obj.buckets_.source_ = _a[1];
            _b = __read([obj.buckets_, this.buckets_], 2), this.buckets_ = _b[0], obj.buckets_ = _b[1];
            var _a, _b;
        };
        return HashMultiMap;
    }(std.base.MultiMap));
    std.HashMultiMap = HashMultiMap;
})(std || (std = {}));
(function (std) {
    var HashMultiMap;
    (function (HashMultiMap) {
        HashMultiMap.Iterator = std.base.MapIterator;
        HashMultiMap.ReverseIterator = std.base.MapReverseIterator;
        HashMultiMap.iterator = HashMultiMap.Iterator;
        HashMultiMap.reverse_iterator = HashMultiMap.ReverseIterator;
    })(HashMultiMap = std.HashMultiMap || (std.HashMultiMap = {}));
})(std || (std = {}));
var std;
(function (std) {
    var Stack = (function () {
        function Stack(stack) {
            if (stack === void 0) { stack = null; }
            this.container_ = new std.List();
            if (stack != null)
                this.container_.assign(stack.container_.begin(), stack.container_.end());
        }
        Stack.prototype.size = function () {
            return this.container_.size();
        };
        Stack.prototype.empty = function () {
            return this.container_.empty();
        };
        Stack.prototype.top = function () {
            return this.container_.back();
        };
        Stack.prototype.push = function (val) {
            this.container_.push_back(val);
        };
        Stack.prototype.pop = function () {
            this.container_.pop_back();
        };
        Stack.prototype.swap = function (obj) {
            this.container_.swap(obj.container_);
        };
        return Stack;
    }());
    std.Stack = Stack;
})(std || (std = {}));
var std;
(function (std) {
    var Queue = (function () {
        function Queue(queue) {
            if (queue === void 0) { queue = null; }
            this.container_ = new std.List();
            if (queue != null)
                this.container_.assign(queue.container_.begin(), queue.container_.end());
        }
        Queue.prototype.size = function () {
            return this.container_.size();
        };
        Queue.prototype.empty = function () {
            return this.container_.empty();
        };
        Queue.prototype.front = function () {
            return this.container_.front();
        };
        Queue.prototype.back = function () {
            return this.container_.back();
        };
        Queue.prototype.push = function (val) {
            this.container_.push_back(val);
        };
        Queue.prototype.pop = function () {
            this.container_.pop_front();
        };
        Queue.prototype.swap = function (obj) {
            this.container_.swap(obj.container_);
        };
        return Queue;
    }());
    std.Queue = Queue;
})(std || (std = {}));
var std;
(function (std) {
    var PriorityQueue = (function () {
        function PriorityQueue() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = this;
            var comp = std.less;
            var post_process = null;
            if (args.length == 1 && args[0] instanceof PriorityQueue) {
                var obj_1 = args[0];
                comp = obj_1.container_.key_comp();
                post_process = function () {
                    var first = obj_1.container_.begin();
                    var last = obj_1.container_.end();
                    _this.container_.assign(first, last);
                };
            }
            else if (args.length >= 2 && args[0].next instanceof Function && args[1].next instanceof Function) {
                if (args.length == 3)
                    comp = args[2];
                post_process = function () {
                    var first = args[0];
                    var last = args[1];
                    _this.container_.assign(first, last);
                };
            }
            else if (args.length == 1)
                comp = args[0];
            this.container_ = new std.TreeMultiSet(comp);
            if (post_process != null)
                post_process();
        }
        PriorityQueue.prototype.size = function () {
            return this.container_.size();
        };
        PriorityQueue.prototype.empty = function () {
            return this.container_.empty();
        };
        PriorityQueue.prototype.top = function () {
            return this.container_.begin().value;
        };
        PriorityQueue.prototype.push = function (val) {
            this.container_.insert(val);
        };
        PriorityQueue.prototype.pop = function () {
            this.container_.erase(this.container_.begin());
        };
        PriorityQueue.prototype.swap = function (obj) {
            this.container_.swap(obj.container_);
        };
        return PriorityQueue;
    }());
    std.PriorityQueue = PriorityQueue;
})(std || (std = {}));
var std;
(function (std) {
    var Exception = (function (_super) {
        __extends(Exception, _super);
        function Exception(message) {
            if (message === void 0) { message = ""; }
            return _super.call(this, message) || this;
        }
        Exception.prototype.what = function () {
            return this.message;
        };
        return Exception;
    }(Error));
    std.Exception = Exception;
})(std || (std = {}));
var std;
(function (std) {
    var LogicError = (function (_super) {
        __extends(LogicError, _super);
        function LogicError(message) {
            return _super.call(this, message) || this;
        }
        return LogicError;
    }(std.Exception));
    std.LogicError = LogicError;
    var DomainError = (function (_super) {
        __extends(DomainError, _super);
        function DomainError(message) {
            return _super.call(this, message) || this;
        }
        return DomainError;
    }(LogicError));
    std.DomainError = DomainError;
    var InvalidArgument = (function (_super) {
        __extends(InvalidArgument, _super);
        function InvalidArgument(message) {
            return _super.call(this, message) || this;
        }
        return InvalidArgument;
    }(LogicError));
    std.InvalidArgument = InvalidArgument;
    var LengthError = (function (_super) {
        __extends(LengthError, _super);
        function LengthError(message) {
            return _super.call(this, message) || this;
        }
        return LengthError;
    }(LogicError));
    std.LengthError = LengthError;
    var OutOfRange = (function (_super) {
        __extends(OutOfRange, _super);
        function OutOfRange(message) {
            return _super.call(this, message) || this;
        }
        return OutOfRange;
    }(LogicError));
    std.OutOfRange = OutOfRange;
})(std || (std = {}));
var std;
(function (std) {
    var RuntimeError = (function (_super) {
        __extends(RuntimeError, _super);
        function RuntimeError(message) {
            return _super.call(this, message) || this;
        }
        return RuntimeError;
    }(std.Exception));
    std.RuntimeError = RuntimeError;
    var OverflowError = (function (_super) {
        __extends(OverflowError, _super);
        function OverflowError(message) {
            return _super.call(this, message) || this;
        }
        return OverflowError;
    }(RuntimeError));
    std.OverflowError = OverflowError;
    var UnderflowError = (function (_super) {
        __extends(UnderflowError, _super);
        function UnderflowError(message) {
            return _super.call(this, message) || this;
        }
        return UnderflowError;
    }(RuntimeError));
    std.UnderflowError = UnderflowError;
    var RangeError = (function (_super) {
        __extends(RangeError, _super);
        function RangeError(message) {
            return _super.call(this, message) || this;
        }
        return RangeError;
    }(RuntimeError));
    std.RangeError = RangeError;
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var ErrorInstance = (function () {
            function ErrorInstance(val, category) {
                if (val === void 0) { val = 0; }
                if (category === void 0) { category = null; }
                this.assign(val, category);
            }
            ErrorInstance.prototype.assign = function (val, category) {
                this.category_ = category;
                this.value_ = val;
            };
            ErrorInstance.prototype.clear = function () {
                this.value_ = 0;
            };
            ErrorInstance.prototype.category = function () {
                return this.category_;
            };
            ErrorInstance.prototype.value = function () {
                return this.value_;
            };
            ErrorInstance.prototype.message = function () {
                if (this.category_ == null || this.value_ == 0)
                    return "";
                else
                    return this.category_.message(this.value_);
            };
            ErrorInstance.prototype.default_error_condition = function () {
                if (this.category_ == null || this.value_ == 0)
                    return null;
                else
                    return this.category_.default_error_condition(this.value_);
            };
            ErrorInstance.prototype.to_bool = function () {
                return this.value_ != 0;
            };
            return ErrorInstance;
        }());
        base.ErrorInstance = ErrorInstance;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var SystemError = (function (_super) {
        __extends(SystemError, _super);
        function SystemError() {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var _this = _super.call(this, "") || this;
            if (args.length >= 2 && typeof args[0] == "number") {
                var val = args[0];
                var category = args[1];
                _this.code_ = new std.ErrorCode(val, category);
            }
            else {
                _this.code_ = args[0];
            }
            return _this;
        }
        SystemError.prototype.code = function () {
            return this.code_;
        };
        return SystemError;
    }(std.RuntimeError));
    std.SystemError = SystemError;
})(std || (std = {}));
(function (std) {
    var ErrorCategory = (function () {
        function ErrorCategory() {
        }
        ErrorCategory.prototype.default_error_condition = function (val) {
            return new std.ErrorCondition(val, this);
        };
        ErrorCategory.prototype.equivalent = function () {
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            if (args[1] instanceof std.ErrorCondition) {
                var val_code = args[0];
                var cond = args[1];
                return std.equal_to(this.default_error_condition(val_code), cond);
            }
            else {
                var code = args[0];
                var valcond = args[1];
                return std.equal_to(this, code.category()) && code.value() == valcond;
            }
        };
        return ErrorCategory;
    }());
    std.ErrorCategory = ErrorCategory;
})(std || (std = {}));
(function (std) {
    var ErrorCondition = (function (_super) {
        __extends(ErrorCondition, _super);
        function ErrorCondition(val, category) {
            if (val === void 0) { val = 0; }
            if (category === void 0) { category = null; }
            return _super.call(this, val, category) || this;
        }
        return ErrorCondition;
    }(std.base.ErrorInstance));
    std.ErrorCondition = ErrorCondition;
})(std || (std = {}));
(function (std) {
    var ErrorCode = (function (_super) {
        __extends(ErrorCode, _super);
        function ErrorCode(val, category) {
            if (val === void 0) { val = 0; }
            if (category === void 0) { category = null; }
            return _super.call(this, val, category) || this;
        }
        return ErrorCode;
    }(std.base.ErrorInstance));
    std.ErrorCode = ErrorCode;
})(std || (std = {}));
var std;
(function (std) {
    function terminate() {
        if (_Terminate_handler != null)
            _Terminate_handler();
        if (std.is_node() == true)
            process.exit();
        else {
            window.open("", "_self", "");
            window.close();
        }
    }
    std.terminate = terminate;
    function set_terminate(f) {
        _Terminate_handler = f;
        if (std.is_node() == true)
            process.on("uncaughtException", function () {
                _Terminate_handler();
            });
        else
            window.onerror = function () {
                _Terminate_handler();
            };
    }
    std.set_terminate = set_terminate;
    function get_terminate() {
        return _Terminate_handler;
    }
    std.get_terminate = get_terminate;
    var _Terminate_handler = null;
})(std || (std = {}));
var std;
(function (std) {
    function logical_and(x, y) {
        return x && y;
    }
    std.logical_and = logical_and;
    function logical_or(x, y) {
        return x || y;
    }
    std.logical_or = logical_or;
    function logical_not(x) {
        return !x;
    }
    std.logical_not = logical_not;
    function bit_and(x, y) {
        return x & y;
    }
    std.bit_and = bit_and;
    function bit_or(x, y) {
        return x | y;
    }
    std.bit_or = bit_or;
    function bit_xor(x, y) {
        return x ^ y;
    }
    std.bit_xor = bit_xor;
})(std || (std = {}));
var std;
(function (std) {
    function equal_to(x, y) {
        if (x instanceof Object) {
            if (x.equals)
                return x.equals(y);
            else
                return x == y;
        }
        else
            return x == y;
    }
    std.equal_to = equal_to;
    function not_equal_to(x, y) {
        return !equal_to(x, y);
    }
    std.not_equal_to = not_equal_to;
    function less(x, y) {
        if (x instanceof Object)
            if (x.less != undefined)
                return x.less(y);
            else
                return x.__get_m_iUID() < y.__get_m_iUID();
        else
            return x < y;
    }
    std.less = less;
    function less_equal(x, y) {
        return less(x, y) || equal_to(x, y);
    }
    std.less_equal = less_equal;
    function greater(x, y) {
        return !less_equal(x, y);
    }
    std.greater = greater;
    function greater_equal(x, y) {
        return !less(x, y);
    }
    std.greater_equal = greater_equal;
})(std || (std = {}));
var std;
(function (std) {
    function hash(val) {
        var args = [];
        for (var _i = 1; _i < arguments.length; _i++) {
            args[_i - 1] = arguments[_i];
        }
        args.unshift(val);
        var ret = _HASH_INIT_VALUE;
        try {
            for (var args_1 = __values(args), args_1_1 = args_1.next(); !args_1_1.done; args_1_1 = args_1.next()) {
                var item = args_1_1.value;
                var type = typeof item;
                if (type == "boolean")
                    ret = _Hash_boolean(item, ret);
                else if (type == "number")
                    ret = _Hash_number(item, ret);
                else if (type == "string")
                    ret = _Hash_string(item, ret);
                else {
                    if (item.hashCode != undefined) {
                        var hashed = item.hashCode();
                        if (args.length == 1)
                            return hashed;
                        else {
                            ret = ret ^ hashed;
                            ret *= _HASH_MULTIPLIER;
                        }
                    }
                    else
                        ret = _Hash_number(item.__get_m_iUID(), ret);
                }
            }
        }
        catch (e_4_1) { e_4 = { error: e_4_1 }; }
        finally {
            try {
                if (args_1_1 && !args_1_1.done && (_a = args_1.return)) _a.call(args_1);
            }
            finally { if (e_4) throw e_4.error; }
        }
        return ret;
        var e_4, _a;
    }
    std.hash = hash;
    function _Hash_boolean(val, ret) {
        ret ^= val ? 1 : 0;
        ret *= _HASH_MULTIPLIER;
        return ret;
    }
    function _Hash_number(val, ret) {
        var buffer = new ArrayBuffer(8);
        var byteArray = new Int8Array(buffer);
        var valueArray = new Float64Array(buffer);
        valueArray[0] = val;
        for (var i = 0; i < byteArray.length; i++) {
            var byte = (byteArray[i] < 0) ? byteArray[i] + 256 : byteArray[i];
            ret ^= byte;
            ret *= _HASH_MULTIPLIER;
        }
        return Math.abs(ret);
    }
    function _Hash_string(str, ret) {
        for (var i = 0; i < str.length; i++) {
            ret ^= str.charCodeAt(i);
            ret *= _HASH_MULTIPLIER;
        }
        return Math.abs(ret);
    }
    var _HASH_INIT_VALUE = 2166136261;
    var _HASH_MULTIPLIER = 16777619;
    var __s_iUID = 0;
    if (Object.prototype.hasOwnProperty("__get_m_iUID") == false) {
        Object.defineProperties(Object.prototype, {
            "__get_m_iUID": {
                value: function () {
                    if (this.hasOwnProperty("__m_iUID") == false) {
                        var uid = ++__s_iUID;
                        Object.defineProperty(this, "__m_iUID", {
                            "get": function () {
                                return uid;
                            }
                        });
                    }
                    return this.__m_iUID;
                }
            }
        });
    }
})(std || (std = {}));
var std;
(function (std) {
    function swap(x, y) {
        x.swap(y);
    }
    std.swap = swap;
})(std || (std = {}));
var std;
(function (std) {
    function size(container) {
        return container.size();
    }
    std.size = size;
    function empty(container) {
        return container.empty();
    }
    std.empty = empty;
    function distance(first, last) {
        if (first.index != undefined)
            return _Distance_via_index(first, last);
        var length = 0;
        for (; !first.equals(last); first = first.next())
            length++;
        return length;
    }
    std.distance = distance;
    function _Distance_via_index(first, last) {
        var start = first.index();
        var end = last.index();
        if (start == -1)
            start = first.source().size();
        else if (end == -1)
            end = first.source().size();
        return Math.abs(end - start);
    }
    function advance(it, n) {
        return it.advance(n);
    }
    std.advance = advance;
    function prev(it, n) {
        if (n === void 0) { n = 1; }
        return it.advance(-n);
    }
    std.prev = prev;
    function next(it, n) {
        if (n === void 0) { n = 1; }
        return it.advance(n);
    }
    std.next = next;
    function begin(container) {
        if (container instanceof Array) {
            var vec = new std.Vector();
            vec.data_ = container;
            return vec.begin();
        }
        else
            return container.begin();
    }
    std.begin = begin;
    function end(container) {
        if (container instanceof Array) {
            var vec = new std.Vector();
            vec.data_ = container;
            return vec.end();
        }
        else
            return container.end();
    }
    std.end = end;
    function make_reverse_iterator(it) {
        if (it instanceof std.base.ArrayIterator)
            return new std.base.ArrayReverseIterator(it);
        else if (it instanceof std.List.Iterator)
            return new std.List.ReverseIterator(it);
        else if (it instanceof std.base.SetIterator)
            return new std.base.SetReverseIterator(it);
        else if (it instanceof std.base.MapIterator)
            return new std.base.MapReverseIterator(it);
    }
    std.make_reverse_iterator = make_reverse_iterator;
    function rbegin(container) {
        make_reverse_iterator(end(container));
    }
    std.rbegin = rbegin;
    function rend(container) {
        return make_reverse_iterator(begin(container));
    }
    std.rend = rend;
})(std || (std = {}));
var std;
(function (std) {
    var Mutex = (function () {
        function Mutex() {
            this.lock_count_ = 0;
            this.resolvers_ = new std.Queue();
        }
        Mutex.prototype.lock = function () {
            var _this = this;
            return new Promise(function (resolve) {
                if (_this.lock_count_++ == 0)
                    resolve();
                else
                    _this.resolvers_.push(resolve);
            });
        };
        Mutex.prototype.try_lock = function () {
            if (this.lock_count_ != 0)
                return false;
            ++this.lock_count_;
            return true;
        };
        Mutex.prototype.unlock = function () {
            return __awaiter(this, void 0, void 0, function () {
                var fn;
                return __generator(this, function (_a) {
                    if (this.lock_count_ == 0)
                        throw new std.RangeError("This mutex is free.");
                    --this.lock_count_;
                    if (this.resolvers_.empty() == false) {
                        fn = this.resolvers_.front();
                        this.resolvers_.pop();
                        fn();
                    }
                    return [2];
                });
            });
        };
        return Mutex;
    }());
    std.Mutex = Mutex;
})(std || (std = {}));
var std;
(function (std) {
    var SharedMutex = (function () {
        function SharedMutex() {
            this.read_lock_count_ = 0;
            this.write_lock_count_ = 0;
            this.resolvers_ = new std.Queue();
        }
        SharedMutex.prototype.lock = function () {
            var _this = this;
            return new Promise(function (resolve) {
                if (_this.write_lock_count_++ == 0 && _this.read_lock_count_ == 0)
                    resolve();
                else
                    _this.resolvers_.push(std.make_pair(std.base._LockType.WRITE, resolve));
            });
        };
        SharedMutex.prototype.try_lock = function () {
            if (this.write_lock_count_ != 0 || this.read_lock_count_ != 0)
                return false;
            this.write_lock_count_++;
            return true;
        };
        SharedMutex.prototype.unlock = function () {
            return __awaiter(this, void 0, void 0, function () {
                var access, fn;
                return __generator(this, function (_a) {
                    if (this.write_lock_count_ == 0)
                        throw new std.RangeError("This mutex is free on the unique lock.");
                    while (this.resolvers_.empty() == false) {
                        access = this.resolvers_.front().first;
                        fn = this.resolvers_.front().second;
                        this.resolvers_.pop();
                        fn();
                        if (access == std.base._LockType.WRITE)
                            break;
                    }
                    --this.write_lock_count_;
                    return [2];
                });
            });
        };
        SharedMutex.prototype.lock_shared = function () {
            var _this = this;
            return new Promise(function (resolve) {
                ++_this.read_lock_count_;
                if (_this.write_lock_count_ == 0)
                    resolve();
                else
                    _this.resolvers_.push(std.make_pair(std.base._LockType.READ, resolve));
            });
        };
        SharedMutex.prototype.try_lock_shared = function () {
            if (this.write_lock_count_ != 0)
                return false;
            ++this.read_lock_count_;
            return true;
        };
        SharedMutex.prototype.unlock_shared = function () {
            return __awaiter(this, void 0, void 0, function () {
                var fn;
                return __generator(this, function (_a) {
                    if (this.read_lock_count_ == 0)
                        throw new std.RangeError("This mutex is free on the shared lock.");
                    --this.read_lock_count_;
                    if (this.resolvers_.empty() == false) {
                        fn = this.resolvers_.front().second;
                        this.resolvers_.pop();
                        fn();
                    }
                    return [2];
                });
            });
        };
        return SharedMutex;
    }());
    std.SharedMutex = SharedMutex;
})(std || (std = {}));
var std;
(function (std) {
    var TimedMutex = (function () {
        function TimedMutex() {
            this.lock_count_ = 0;
            this.resolvers_ = new std.HashMap();
        }
        TimedMutex.prototype.lock = function () {
            var _this = this;
            return new Promise(function (resolve) {
                if (_this.lock_count_++ == 0)
                    resolve();
                else
                    _this.resolvers_.emplace(resolve, std.base._LockType.LOCK);
            });
        };
        TimedMutex.prototype.try_lock = function () {
            if (this.lock_count_ != 0)
                return false;
            ++this.lock_count_;
            return true;
        };
        TimedMutex.prototype.unlock = function () {
            return __awaiter(this, void 0, void 0, function () {
                var it, listener;
                return __generator(this, function (_a) {
                    if (this.lock_count_ == 0)
                        throw new std.RangeError("This mutex is free.");
                    --this.lock_count_;
                    if (this.resolvers_.empty() == false) {
                        it = this.resolvers_.begin();
                        listener = it.first;
                        this.resolvers_.erase(it);
                        if (it.second == std.base._LockType.LOCK)
                            listener();
                        else
                            listener(true);
                    }
                    return [2];
                });
            });
        };
        TimedMutex.prototype.try_lock_for = function (ms) {
            var _this = this;
            return new Promise(function (resolve) {
                if (_this.lock_count_++ == 0)
                    resolve(true);
                else {
                    _this.resolvers_.emplace(resolve, std.base._LockType.TRY_LOCK);
                    std.sleep_for(ms).then(function () {
                        if (_this.resolvers_.has(resolve) == false)
                            return;
                        _this.resolvers_.erase(resolve);
                        --_this.lock_count_;
                        resolve(false);
                    });
                }
            });
        };
        TimedMutex.prototype.try_lock_until = function (at) {
            var now = new Date();
            var ms = at.getTime() - now.getTime();
            return this.try_lock_for(ms);
        };
        return TimedMutex;
    }());
    std.TimedMutex = TimedMutex;
})(std || (std = {}));
var std;
(function (std) {
    var SharedTimedMutex = (function () {
        function SharedTimedMutex() {
            this.read_lock_count_ = 0;
            this.write_lock_count_ = 0;
            this.resolvers_ = new std.HashMap();
        }
        SharedTimedMutex.prototype.lock = function () {
            var _this = this;
            return new Promise(function (resolve) {
                if (_this.write_lock_count_++ == 0 && _this.read_lock_count_ == 0)
                    resolve();
                else
                    _this.resolvers_.emplace(resolve, {
                        access: std.base._LockType.WRITE,
                        lock: std.base._LockType.LOCK
                    });
            });
        };
        SharedTimedMutex.prototype.try_lock = function () {
            if (this.write_lock_count_ != 0 || this.read_lock_count_ != 0)
                return false;
            ++this.write_lock_count_;
            return true;
        };
        SharedTimedMutex.prototype.try_lock_for = function (ms) {
            var _this = this;
            return new Promise(function (resolve) {
                if (_this.write_lock_count_++ == 0 && _this.read_lock_count_ == 0)
                    resolve(true);
                else {
                    _this.resolvers_.emplace(resolve, {
                        access: std.base._LockType.WRITE,
                        lock: std.base._LockType.TRY_LOCK
                    });
                    std.sleep_for(ms).then(function () {
                        if (_this.resolvers_.has(resolve) == false)
                            return;
                        _this.resolvers_.erase(resolve);
                        --_this.write_lock_count_;
                        resolve(false);
                    });
                }
            });
        };
        SharedTimedMutex.prototype.try_lock_until = function (at) {
            var now = new Date();
            var ms = at.getTime() - now.getTime();
            return this.try_lock_for(ms);
        };
        SharedTimedMutex.prototype.unlock = function () {
            return __awaiter(this, void 0, void 0, function () {
                var it, listener, type;
                return __generator(this, function (_a) {
                    if (this.write_lock_count_ == 0)
                        throw new std.RangeError("This mutex is free on the unique lock.");
                    while (this.resolvers_.empty() == false) {
                        it = this.resolvers_.begin();
                        listener = it.first;
                        type = it.second;
                        this.resolvers_.erase(it);
                        if (type.lock == std.base._LockType.LOCK)
                            listener();
                        else
                            listener(true);
                        if (type.access == std.base._LockType.WRITE)
                            break;
                    }
                    --this.write_lock_count_;
                    return [2];
                });
            });
        };
        SharedTimedMutex.prototype.lock_shared = function () {
            var _this = this;
            return new Promise(function (resolve) {
                ++_this.read_lock_count_;
                if (_this.write_lock_count_ == 0)
                    resolve();
                else
                    _this.resolvers_.emplace(resolve, {
                        access: std.base._LockType.READ,
                        lock: std.base._LockType.LOCK
                    });
            });
        };
        SharedTimedMutex.prototype.try_lock_shared = function () {
            if (this.write_lock_count_ != 0)
                return false;
            ++this.read_lock_count_;
            return true;
        };
        SharedTimedMutex.prototype.try_lock_shared_for = function (ms) {
            var _this = this;
            return new Promise(function (resolve) {
                ++_this.read_lock_count_;
                if (_this.write_lock_count_ == 0)
                    resolve(true);
                else {
                    _this.resolvers_.emplace(resolve, {
                        access: std.base._LockType.READ,
                        lock: std.base._LockType.TRY_LOCK
                    });
                    std.sleep_for(ms).then(function () {
                        if (_this.resolvers_.has(resolve) == false)
                            return;
                        _this.resolvers_.erase(resolve);
                        --_this.read_lock_count_;
                        resolve(false);
                    });
                }
            });
        };
        SharedTimedMutex.prototype.try_lock_shared_until = function (at) {
            var now = new Date();
            var ms = at.getTime() - now.getTime();
            return this.try_lock_shared_for(ms);
        };
        SharedTimedMutex.prototype.unlock_shared = function () {
            return __awaiter(this, void 0, void 0, function () {
                var it, listener, type;
                return __generator(this, function (_a) {
                    if (this.read_lock_count_ == 0)
                        throw new std.RangeError("This mutex is free on the shared lock.");
                    --this.read_lock_count_;
                    if (this.resolvers_.empty() == false) {
                        it = this.resolvers_.begin();
                        listener = it.first;
                        type = it.second;
                        this.resolvers_.erase(it);
                        if (type.lock == std.base._LockType.LOCK)
                            listener();
                        else
                            listener(true);
                    }
                    return [2];
                });
            });
        };
        return SharedTimedMutex;
    }());
    std.SharedTimedMutex = SharedTimedMutex;
})(std || (std = {}));
var std;
(function (std) {
    var ConditionVariable = (function () {
        function ConditionVariable() {
            this.resolvers_ = new std.HashMap();
        }
        ConditionVariable.prototype.wait = function () {
            var _this = this;
            return new Promise(function (resolve) {
                _this.resolvers_.emplace(resolve, std.base._LockType.LOCK);
            });
        };
        ConditionVariable.prototype.wait_for = function (ms) {
            var _this = this;
            return new Promise(function (resolve) {
                _this.resolvers_.emplace(resolve, std.base._LockType.TRY_LOCK);
                std.sleep_for(ms).then(function () {
                    if (_this.resolvers_.has(resolve) == false)
                        return;
                    _this.resolvers_.erase(resolve);
                    resolve(false);
                });
            });
        };
        ConditionVariable.prototype.wait_until = function (at) {
            var now = new Date();
            var ms = at.getTime() - now.getTime();
            return this.wait_for(ms);
        };
        ConditionVariable.prototype.notify_one = function () {
            return __awaiter(this, void 0, void 0, function () {
                var it;
                return __generator(this, function (_a) {
                    if (this.resolvers_.empty())
                        return [2];
                    it = this.resolvers_.begin();
                    if (it.second == std.base._LockType.LOCK)
                        it.first();
                    else
                        it.first(true);
                    this.resolvers_.erase(it);
                    return [2];
                });
            });
        };
        ConditionVariable.prototype.notify_all = function () {
            return __awaiter(this, void 0, void 0, function () {
                var _a, _b, pair, e_5, _c;
                return __generator(this, function (_d) {
                    if (this.resolvers_.empty())
                        return [2];
                    try {
                        for (_a = __values(this.resolvers_), _b = _a.next(); !_b.done; _b = _a.next()) {
                            pair = _b.value;
                            if (pair.second == std.base._LockType.LOCK)
                                pair.first();
                            else
                                pair.first(true);
                        }
                    }
                    catch (e_5_1) { e_5 = { error: e_5_1 }; }
                    finally {
                        try {
                            if (_b && !_b.done && (_c = _a.return)) _c.call(_a);
                        }
                        finally { if (e_5) throw e_5.error; }
                    }
                    this.resolvers_.clear();
                    return [2];
                });
            });
        };
        return ConditionVariable;
    }());
    std.ConditionVariable = ConditionVariable;
})(std || (std = {}));
var std;
(function (std) {
    var experiments;
    (function (experiments) {
        var Semaphore = (function () {
            function Semaphore(size) {
                this.hold_count_ = 0;
                this.locked_count_ = 0;
                this.size_ = size;
                this.listeners_ = new std.Queue();
            }
            Semaphore.prototype.size = function () {
                return this.size_;
            };
            Semaphore.prototype._Compute_excess_count = function (count) {
                return Math.max(0, Math.min(this.locked_count_, this.size_) + count - this.size_);
            };
            Semaphore.prototype.lock = function (count) {
                var _this = this;
                if (count === void 0) { count = 1; }
                return new Promise(function (resolve, reject) {
                    if (count < 1 || count > _this.size_) {
                        reject(new std.OutOfRange("Lock count to semaphore is out of its range."));
                        return;
                    }
                    var exceeded_count = _this._Compute_excess_count(count);
                    _this.hold_count_ += exceeded_count;
                    _this.locked_count_ += count;
                    if (exceeded_count > 0)
                        _this.listeners_.push(std.make_pair(resolve, exceeded_count));
                    else
                        resolve();
                });
            };
            Semaphore.prototype.try_lock = function (count) {
                if (count === void 0) { count = 1; }
                if (count < 1 || count > this.size_)
                    throw new std.OutOfRange("Lock count to semaphore is out of its range.");
                if (this.locked_count_ + count > this.size_)
                    return false;
                this.locked_count_ += count;
                return true;
            };
            Semaphore.prototype.unlock = function (count) {
                if (count === void 0) { count = 1; }
                return __awaiter(this, void 0, void 0, function () {
                    var resolved_count, front, fn;
                    return __generator(this, function (_a) {
                        if (count < 1 || count > this.size_)
                            throw new std.OutOfRange("Unlock count to semaphore is out of its range.");
                        else if (count > this.locked_count_)
                            throw new std.RangeError("Number of unlocks to semaphore is greater than its locks.");
                        resolved_count = Math.min(count, this.hold_count_);
                        this.hold_count_ -= resolved_count;
                        this.locked_count_ -= count;
                        while (resolved_count != 0) {
                            front = this.listeners_.front();
                            if (front.second > resolved_count) {
                                front.second -= resolved_count;
                                resolved_count = 0;
                            }
                            else {
                                fn = front.first;
                                resolved_count -= front.second;
                                this.listeners_.pop();
                                fn();
                            }
                        }
                        return [2];
                    });
                });
            };
            return Semaphore;
        }());
        experiments.Semaphore = Semaphore;
    })(experiments = std.experiments || (std.experiments = {}));
})(std || (std = {}));
var std;
(function (std) {
    var experiments;
    (function (experiments) {
        var TimedSemaphore = (function () {
            function TimedSemaphore(size) {
                this.locked_count_ = 0;
                this.hold_count_ = 0;
                this.size_ = size;
                this.resolvers_ = new std.HashMap();
            }
            TimedSemaphore.prototype.size = function () {
                return this.size_;
            };
            TimedSemaphore.prototype._Compute_excess_count = function (count) {
                return Math.max(0, Math.min(this.locked_count_, this.size_) + count - this.size_);
            };
            TimedSemaphore.prototype._Compute_resolve_count = function (count) {
                return Math.min(count, this.hold_count_);
            };
            TimedSemaphore.prototype.lock = function (count) {
                var _this = this;
                if (count === void 0) { count = 1; }
                return new Promise(function (resolve, reject) {
                    if (count < 1 || count > _this.size_) {
                        reject(new std.OutOfRange("Lock count to semaphore is out of its range."));
                        return;
                    }
                    var exceeded_count = _this._Compute_excess_count(count);
                    _this.hold_count_ += exceeded_count;
                    _this.locked_count_ += count;
                    if (exceeded_count > 0)
                        _this.resolvers_.emplace(resolve, {
                            count: exceeded_count,
                            type: std.base._LockType.LOCK
                        });
                    else
                        resolve();
                });
            };
            TimedSemaphore.prototype.try_lock = function (count) {
                if (count === void 0) { count = 1; }
                if (count < 1 || count > this.size_)
                    throw new std.OutOfRange("Lock count to semaphore is out of its range.");
                if (this.locked_count_ + count > this.size_)
                    return false;
                this.locked_count_ += count;
                return true;
            };
            TimedSemaphore.prototype.unlock = function (count) {
                if (count === void 0) { count = 1; }
                return __awaiter(this, void 0, void 0, function () {
                    return __generator(this, function (_a) {
                        switch (_a.label) {
                            case 0:
                                if (count < 1 || count > this.size_)
                                    throw new std.OutOfRange("Unlock count to semaphore is out of its range.");
                                else if (count > this.locked_count_)
                                    throw new std.RangeError("Number of unlocks to semaphore is greater than its locks.");
                                this.locked_count_ -= count;
                                return [4, this._Unlock(count)];
                            case 1:
                                _a.sent();
                                return [2];
                        }
                    });
                });
            };
            TimedSemaphore.prototype._Unlock = function (resolved_count) {
                return __awaiter(this, void 0, void 0, function () {
                    var it, props;
                    return __generator(this, function (_a) {
                        resolved_count = this._Compute_resolve_count(resolved_count);
                        this.hold_count_ -= resolved_count;
                        while (resolved_count != 0) {
                            it = this.resolvers_.begin();
                            props = it.second;
                            if (props.count > resolved_count) {
                                props.count -= resolved_count;
                                resolved_count = 0;
                            }
                            else {
                                resolved_count -= props.count;
                                this.resolvers_.erase(it);
                                if (props.type == std.base._LockType.LOCK)
                                    it.first();
                                else
                                    it.first(true);
                            }
                        }
                        return [2];
                    });
                });
            };
            TimedSemaphore.prototype.try_lock_for = function (ms, count) {
                if (count === void 0) { count = 1; }
                return __awaiter(this, void 0, void 0, function () {
                    var _this = this;
                    return __generator(this, function (_a) {
                        return [2, new Promise(function (resolve, reject) {
                                if (count < 1 || count > _this.size_) {
                                    reject(new std.OutOfRange("Lock count to semaphore is out of its range."));
                                    return;
                                }
                                var exceeded_count = _this._Compute_excess_count(count);
                                _this.hold_count_ += exceeded_count;
                                _this.locked_count_ += count;
                                if (exceeded_count > 0) {
                                    _this.resolvers_.emplace(resolve, {
                                        count: exceeded_count,
                                        type: std.base._LockType.TRY_LOCK
                                    });
                                    std.sleep_for(ms).then(function () {
                                        var it = _this.resolvers_.find(resolve);
                                        if (it.equals(_this.resolvers_.end()) == true)
                                            return;
                                        _this.locked_count_ -= count - (exceeded_count - it.second.count);
                                        _this.hold_count_ -= it.second.count;
                                        _this.resolvers_.erase(it);
                                        _this._Unlock(it.second.count).then(function () {
                                            resolve(false);
                                        });
                                    });
                                }
                                else
                                    resolve(true);
                            })];
                    });
                });
            };
            TimedSemaphore.prototype.try_lock_until = function (at, count) {
                if (count === void 0) { count = 1; }
                var now = new Date();
                var ms = at.getTime() - now.getTime();
                return this.try_lock_for(ms, count);
            };
            return TimedSemaphore;
        }());
        experiments.TimedSemaphore = TimedSemaphore;
    })(experiments = std.experiments || (std.experiments = {}));
})(std || (std = {}));
var std;
(function (std) {
    function sleep_for(ms) {
        return new Promise(function (resolve, reject) {
            if (ms < 0)
                reject(new std.InvalidArgument("Unable to sleep by negative duration."));
            else
                setTimeout(function () {
                    resolve();
                }, ms);
        });
    }
    std.sleep_for = sleep_for;
    function sleep_until(at) {
        var now = new Date();
        var ms = at.getTime() - now.getTime();
        return sleep_for(ms);
    }
    std.sleep_until = sleep_until;
    function lock() {
        var items = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            items[_i] = arguments[_i];
        }
        return new Promise(function (resolve) {
            var count = 0;
            try {
                for (var items_1 = __values(items), items_1_1 = items_1.next(); !items_1_1.done; items_1_1 = items_1.next()) {
                    var mtx = items_1_1.value;
                    mtx.lock().then(function () {
                        if (++count == items.length)
                            resolve();
                    });
                }
            }
            catch (e_6_1) { e_6 = { error: e_6_1 }; }
            finally {
                try {
                    if (items_1_1 && !items_1_1.done && (_a = items_1.return)) _a.call(items_1);
                }
                finally { if (e_6) throw e_6.error; }
            }
            var e_6, _a;
        });
    }
    std.lock = lock;
    function try_lock() {
        var items = [];
        for (var _i = 0; _i < arguments.length; _i++) {
            items[_i] = arguments[_i];
        }
        for (var i = 0; i < items.length; ++i)
            if (items[i].try_lock() == false)
                return i;
        return -1;
    }
    std.try_lock = try_lock;
})(std || (std = {}));
var std;
(function (std) {
    var Pair = (function () {
        function Pair(first, second) {
            this.first = first;
            this.second = second;
        }
        Pair.prototype.equals = function (pair) {
            return std.equal_to(this.first, pair.first) && std.equal_to(this.second, pair.second);
        };
        Pair.prototype.less = function (pair) {
            if (std.equal_to(this.first, pair.first) == false)
                return std.less(this.first, pair.first);
            else
                return std.less(this.second, pair.second);
        };
        Pair.prototype.hashCode = function () {
            return std.hash(this.first, this.second);
        };
        return Pair;
    }());
    std.Pair = Pair;
})(std || (std = {}));
var std;
(function (std) {
    var Entry = (function () {
        function Entry(first, second) {
            this.first = first;
            this.second = second;
        }
        Entry.prototype.equals = function (obj) {
            return std.equal_to(this.first, obj.first);
        };
        Entry.prototype.less = function (obj) {
            return std.less(this.first, obj.first);
        };
        Entry.prototype.hashCode = function () {
            return std.hash(this.first);
        };
        return Entry;
    }());
    std.Entry = Entry;
})(std || (std = {}));
var std;
(function (std) {
    function is_node() {
        if (typeof process === "object")
            if (typeof process.versions === "object")
                if (typeof process.versions.node !== "undefined")
                    return true;
        return false;
    }
    std.is_node = is_node;
    function make_pair(x, y) {
        return new std.Pair(x, y);
    }
    std.make_pair = make_pair;
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _MapElementList = (function (_super) {
            __extends(_MapElementList, _super);
            function _MapElementList(associative) {
                var _this = _super.call(this) || this;
                _this.associative_ = associative;
                return _this;
            }
            _MapElementList.prototype._Create_iterator = function (prev, next, val) {
                return new base.MapIterator(this, prev, next, val);
            };
            _MapElementList.prototype._Set_begin = function (it) {
                _super.prototype._Set_begin.call(this, it);
                this.rend_ = new base.MapReverseIterator(it);
            };
            _MapElementList.prototype.associative = function () {
                return this.associative_;
            };
            _MapElementList.prototype.rbegin = function () {
                return new base.MapReverseIterator(this.end());
            };
            _MapElementList.prototype.rend = function () {
                return this.rend_;
            };
            return _MapElementList;
        }(base._ListContainer));
        base._MapElementList = _MapElementList;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _SetElementList = (function (_super) {
            __extends(_SetElementList, _super);
            function _SetElementList(associative) {
                var _this = _super.call(this) || this;
                _this.associative_ = associative;
                return _this;
            }
            _SetElementList.prototype._Create_iterator = function (prev, next, val) {
                return new base.SetIterator(this, prev, next, val);
            };
            _SetElementList.prototype._Set_begin = function (it) {
                _super.prototype._Set_begin.call(this, it);
                this.rend_ = new base.SetReverseIterator(it);
            };
            _SetElementList.prototype.associative = function () {
                return this.associative_;
            };
            _SetElementList.prototype.rbegin = function () {
                return new base.SetReverseIterator(this.end());
            };
            _SetElementList.prototype.rend = function () {
                return this.rend_;
            };
            return _SetElementList;
        }(base._ListContainer));
        base._SetElementList = _SetElementList;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var MIN_BUCKET_COUNT = 10;
        var DEFAULT_MAX_FACTOR = 1.0;
        var _HashBuckets = (function () {
            function _HashBuckets() {
                this.clear();
                this.max_load_factor_ = DEFAULT_MAX_FACTOR;
            }
            _HashBuckets.prototype.clear = function () {
                this.buckets_ = new std.Vector();
                this.item_size_ = 0;
                for (var i = 0; i < MIN_BUCKET_COUNT; ++i)
                    this.buckets_.push_back(new std.Vector());
            };
            _HashBuckets.prototype.rehash = function (size) {
                if (size < MIN_BUCKET_COUNT)
                    size = MIN_BUCKET_COUNT;
                var prev_matrix = this.buckets_;
                this.buckets_ = new std.Vector();
                for (var i = 0; i < size; ++i)
                    this.buckets_.push_back(new std.Vector());
                try {
                    for (var prev_matrix_1 = __values(prev_matrix), prev_matrix_1_1 = prev_matrix_1.next(); !prev_matrix_1_1.done; prev_matrix_1_1 = prev_matrix_1.next()) {
                        var row = prev_matrix_1_1.value;
                        try {
                            for (var row_1 = __values(row), row_1_1 = row_1.next(); !row_1_1.done; row_1_1 = row_1.next()) {
                                var col = row_1_1.value;
                                var index = this.hash_index(col);
                                var bucket = this.buckets_.at(index);
                                bucket.push_back(col);
                                ++this.item_size_;
                            }
                        }
                        catch (e_7_1) { e_7 = { error: e_7_1 }; }
                        finally {
                            try {
                                if (row_1_1 && !row_1_1.done && (_a = row_1.return)) _a.call(row_1);
                            }
                            finally { if (e_7) throw e_7.error; }
                        }
                    }
                }
                catch (e_8_1) { e_8 = { error: e_8_1 }; }
                finally {
                    try {
                        if (prev_matrix_1_1 && !prev_matrix_1_1.done && (_b = prev_matrix_1.return)) _b.call(prev_matrix_1);
                    }
                    finally { if (e_8) throw e_8.error; }
                }
                var e_8, _b, e_7, _a;
            };
            _HashBuckets.prototype.reserve = function (size) {
                this.item_size_ += size;
                if (this.item_size_ > this.capacity())
                    this.rehash(Math.max(this.item_size_, this.capacity() * 2));
            };
            _HashBuckets.prototype.size = function () {
                return this.buckets_.size();
            };
            _HashBuckets.prototype.capacity = function () {
                return this.buckets_.size() * this.max_load_factor_;
            };
            _HashBuckets.prototype.at = function (index) {
                return this.buckets_.at(index);
            };
            _HashBuckets.prototype.load_factor = function () {
                return this.item_size_ / this.size();
            };
            _HashBuckets.prototype.max_load_factor = function (z) {
                if (z === void 0) { z = null; }
                if (z == null)
                    return this.max_load_factor_;
                else
                    this.max_load_factor_ = z;
            };
            _HashBuckets.prototype.insert = function (val) {
                var capacity = this.capacity();
                if (++this.item_size_ > capacity)
                    this.rehash(capacity * 2);
                var index = this.hash_index(val);
                this.buckets_.at(index).push_back(val);
            };
            _HashBuckets.prototype.erase = function (val) {
                var index = this.hash_index(val);
                var bucket = this.buckets_.at(index);
                for (var i = 0; i < bucket.size(); ++i)
                    if (bucket.at(i) == val) {
                        bucket.erase(bucket.begin().advance(i));
                        --this.item_size_;
                        break;
                    }
            };
            return _HashBuckets;
        }());
        base._HashBuckets = _HashBuckets;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _MapHashBuckets = (function (_super) {
            __extends(_MapHashBuckets, _super);
            function _MapHashBuckets(source, hash, pred) {
                var _this = _super.call(this) || this;
                _this.source_ = source;
                _this.hash_function_ = hash;
                _this.key_eq_ = pred;
                return _this;
            }
            _MapHashBuckets.prototype.hash_function = function () {
                return this.hash_function_;
            };
            _MapHashBuckets.prototype.key_eq = function () {
                return this.key_eq_;
            };
            _MapHashBuckets.prototype.find = function (key) {
                var index = this.hash_function_(key) % this.size();
                var bucket = this.at(index);
                try {
                    for (var bucket_3 = __values(bucket), bucket_3_1 = bucket_3.next(); !bucket_3_1.done; bucket_3_1 = bucket_3.next()) {
                        var it = bucket_3_1.value;
                        if (this.key_eq_(it.first, key))
                            return it;
                    }
                }
                catch (e_9_1) { e_9 = { error: e_9_1 }; }
                finally {
                    try {
                        if (bucket_3_1 && !bucket_3_1.done && (_a = bucket_3.return)) _a.call(bucket_3);
                    }
                    finally { if (e_9) throw e_9.error; }
                }
                return this.source_.end();
                var e_9, _a;
            };
            _MapHashBuckets.prototype.hash_index = function (it) {
                return this.hash_function_(it.first) % this.size();
            };
            return _MapHashBuckets;
        }(base._HashBuckets));
        base._MapHashBuckets = _MapHashBuckets;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _SetHashBuckets = (function (_super) {
            __extends(_SetHashBuckets, _super);
            function _SetHashBuckets(source, hash, pred) {
                var _this = _super.call(this) || this;
                _this.source_ = source;
                _this.hash_function_ = hash;
                _this.key_eq_ = pred;
                return _this;
            }
            _SetHashBuckets.prototype.hash_function = function () {
                return this.hash_function_;
            };
            _SetHashBuckets.prototype.key_eq = function () {
                return this.key_eq_;
            };
            _SetHashBuckets.prototype.find = function (val) {
                var index = this.hash_function_(val) % this.size();
                var bucket = this.at(index);
                try {
                    for (var bucket_4 = __values(bucket), bucket_4_1 = bucket_4.next(); !bucket_4_1.done; bucket_4_1 = bucket_4.next()) {
                        var it = bucket_4_1.value;
                        if (this.key_eq_(it.value, val))
                            return it;
                    }
                }
                catch (e_10_1) { e_10 = { error: e_10_1 }; }
                finally {
                    try {
                        if (bucket_4_1 && !bucket_4_1.done && (_a = bucket_4.return)) _a.call(bucket_4);
                    }
                    finally { if (e_10) throw e_10.error; }
                }
                return this.source_.end();
                var e_10, _a;
            };
            _SetHashBuckets.prototype.hash_index = function (it) {
                return this.hash_function_(it.value) % this.size();
            };
            return _SetHashBuckets;
        }(base._HashBuckets));
        base._SetHashBuckets = _SetHashBuckets;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _DequeForOfAdaptor = (function () {
            function _DequeForOfAdaptor(matrix) {
                this.matrix_ = matrix;
                this.row_ = 0;
                this.col_ = 0;
            }
            _DequeForOfAdaptor.prototype.next = function () {
                if (this.row_ == this.matrix_.length)
                    return {
                        done: true,
                        value: undefined
                    };
                else {
                    var val = this.matrix_[this.row_][this.col_];
                    if (++this.col_ == this.matrix_[this.row_].length) {
                        this.row_++;
                        this.col_ = 0;
                    }
                    return {
                        done: false,
                        value: val
                    };
                }
            };
            _DequeForOfAdaptor.prototype[Symbol.iterator] = function () {
                return this;
            };
            return _DequeForOfAdaptor;
        }());
        base._DequeForOfAdaptor = _DequeForOfAdaptor;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _NativeArrayIterator = (function (_super) {
            __extends(_NativeArrayIterator, _super);
            function _NativeArrayIterator(data, index) {
                var _this = _super.call(this) || this;
                _this.data_ = data;
                _this.index_ = index;
                return _this;
            }
            _NativeArrayIterator.prototype.source = function () {
                return null;
            };
            _NativeArrayIterator.prototype.index = function () {
                return this.index_;
            };
            Object.defineProperty(_NativeArrayIterator.prototype, "value", {
                get: function () {
                    return this.data_[this.index_];
                },
                enumerable: true,
                configurable: true
            });
            _NativeArrayIterator.prototype.prev = function () {
                this.index_--;
                return this;
            };
            _NativeArrayIterator.prototype.next = function () {
                this.index_++;
                return this;
            };
            _NativeArrayIterator.prototype.advance = function (n) {
                this.index_ += n;
                return this;
            };
            _NativeArrayIterator.prototype.equals = function (obj) {
                return this.data_ == obj.data_ && this.index_ == obj.index_;
            };
            _NativeArrayIterator.prototype.swap = function (obj) {
                _a = __read([obj.data_, this.data_], 2), this.data_ = _a[0], obj.data_ = _a[1];
                _b = __read([obj.index_, this.index_], 2), this.index_ = _b[0], obj.index_ = _b[1];
                var _a, _b;
            };
            return _NativeArrayIterator;
        }(base.Iterator));
        base._NativeArrayIterator = _NativeArrayIterator;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _Repeater = (function (_super) {
            __extends(_Repeater, _super);
            function _Repeater(index, value) {
                if (value === void 0) { value = null; }
                var _this = _super.call(this) || this;
                _this.index_ = index;
                _this.value_ = value;
                return _this;
            }
            _Repeater.prototype.source = function () {
                return null;
            };
            _Repeater.prototype.index = function () {
                return this.index_;
            };
            Object.defineProperty(_Repeater.prototype, "value", {
                get: function () {
                    return this.value_;
                },
                enumerable: true,
                configurable: true
            });
            _Repeater.prototype.prev = function () {
                this.index_--;
                return this;
            };
            _Repeater.prototype.next = function () {
                this.index_++;
                return this;
            };
            _Repeater.prototype.advance = function (n) {
                this.index_ += n;
                return this;
            };
            _Repeater.prototype.equals = function (obj) {
                return this.index_ == obj.index_;
            };
            _Repeater.prototype.swap = function (obj) {
                _a = __read([obj.index_, this.index_], 2), this.index_ = _a[0], obj.index_ = _a[1];
                var _a;
            };
            return _Repeater;
        }(base.Iterator));
        base._Repeater = _Repeater;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var ForOfAdaptor = (function () {
            function ForOfAdaptor(first, last) {
                this.it_ = first;
                this.last_ = last;
            }
            ForOfAdaptor.prototype.next = function () {
                if (this.it_.equals(this.last_))
                    return {
                        done: true,
                        value: undefined
                    };
                else {
                    var it = this.it_;
                    this.it_ = this.it_.next();
                    return {
                        done: false,
                        value: it.value
                    };
                }
            };
            ForOfAdaptor.prototype[Symbol.iterator] = function () {
                return this;
            };
            return ForOfAdaptor;
        }());
        base.ForOfAdaptor = ForOfAdaptor;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _LockType = (function () {
            function _LockType() {
            }
            _LockType.WRITE = false;
            _LockType.READ = true;
            _LockType.LOCK = false;
            _LockType.TRY_LOCK = true;
            return _LockType;
        }());
        base._LockType = _LockType;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _Color;
        (function (_Color) {
            _Color[_Color.BLACK = 0] = "BLACK";
            _Color[_Color.RED = 1] = "RED";
        })(_Color = base._Color || (base._Color = {}));
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _XTree = (function () {
            function _XTree(comp) {
                this.root_ = null;
                this.comp_ = comp;
                this.equal_ = function (x, y) {
                    return !comp(x, y) && !comp(y, x);
                };
            }
            _XTree.prototype.clear = function () {
                this.root_ = null;
            };
            _XTree.prototype.root = function () {
                return this.root_;
            };
            _XTree.prototype.get = function (val) {
                var ret = this.nearest(val);
                if (ret == null || !this.equal_(val, ret.value))
                    return null;
                else
                    return ret;
            };
            _XTree.prototype.nearest = function (val) {
                if (this.root_ == null)
                    return null;
                var ret = this.root_;
                while (true) {
                    var my_node = null;
                    if (this.comp_(val, ret.value))
                        my_node = ret.left;
                    else if (this.comp_(ret.value, val))
                        my_node = ret.right;
                    else
                        return ret;
                    if (my_node != null)
                        ret = my_node;
                    else
                        break;
                }
                return ret;
            };
            _XTree.prototype._Fetch_maximum = function (node) {
                while (node.right != null)
                    node = node.right;
                return node;
            };
            _XTree.prototype.insert = function (val) {
                var parent = this.nearest(val);
                var node = new base._XTreeNode(val, base._Color.RED);
                if (parent == null)
                    this.root_ = node;
                else {
                    node.parent = parent;
                    if (this.comp_(node.value, parent.value))
                        parent.left = node;
                    else
                        parent.right = node;
                }
                this._Insert_case1(node);
            };
            _XTree.prototype._Insert_case1 = function (n) {
                if (n.parent == null)
                    n.color = base._Color.BLACK;
                else
                    this._Insert_case2(n);
            };
            _XTree.prototype._Insert_case2 = function (n) {
                if (this._Fetch_color(n.parent) == base._Color.BLACK)
                    return;
                else
                    this._Insert_case3(n);
            };
            _XTree.prototype._Insert_case3 = function (n) {
                if (this._Fetch_color(n.uncle) == base._Color.RED) {
                    n.parent.color = base._Color.BLACK;
                    n.uncle.color = base._Color.BLACK;
                    n.grand.color = base._Color.RED;
                    this._Insert_case1(n.grand);
                }
                else
                    this._Insert_case4(n);
            };
            _XTree.prototype._Insert_case4 = function (n) {
                if (n == n.parent.right && n.parent == n.grand.left) {
                    this._Rotate_left(n.parent);
                    n = n.left;
                }
                else if (n == n.parent.left && n.parent == n.grand.right) {
                    this._Rotate_right(n.parent);
                    n = n.right;
                }
                this._Insert_case5(n);
            };
            _XTree.prototype._Insert_case5 = function (n) {
                n.parent.color = base._Color.BLACK;
                n.grand.color = base._Color.RED;
                if (n == n.parent.left && n.parent == n.grand.left)
                    this._Rotate_right(n.grand);
                else
                    this._Rotate_left(n.grand);
            };
            _XTree.prototype.erase = function (val) {
                var node = this.get(val);
                if (node == null)
                    return;
                if (node.left != null && node.right != null) {
                    var pred = this._Fetch_maximum(node.left);
                    node.value = pred.value;
                    node = pred;
                }
                var child = (node.right == null) ? node.left : node.right;
                if (this._Fetch_color(node) == base._Color.BLACK) {
                    node.color = this._Fetch_color(child);
                    this._Erase_case1(node);
                }
                this._Replace_node(node, child);
                if (this._Fetch_color(this.root_) == base._Color.RED)
                    this.root_.color = base._Color.BLACK;
            };
            _XTree.prototype._Erase_case1 = function (n) {
                if (n.parent == null)
                    return;
                else
                    this._Erase_case2(n);
            };
            _XTree.prototype._Erase_case2 = function (n) {
                if (this._Fetch_color(n.sibling) == base._Color.RED) {
                    n.parent.color = base._Color.RED;
                    n.sibling.color = base._Color.BLACK;
                    if (n == n.parent.left)
                        this._Rotate_left(n.parent);
                    else
                        this._Rotate_right(n.parent);
                }
                this._Erase_case3(n);
            };
            _XTree.prototype._Erase_case3 = function (n) {
                if (this._Fetch_color(n.parent) == base._Color.BLACK &&
                    this._Fetch_color(n.sibling) == base._Color.BLACK &&
                    this._Fetch_color(n.sibling.left) == base._Color.BLACK &&
                    this._Fetch_color(n.sibling.right) == base._Color.BLACK) {
                    n.sibling.color = base._Color.RED;
                    this._Erase_case1(n.parent);
                }
                else
                    this._Erase_case4(n);
            };
            _XTree.prototype._Erase_case4 = function (N) {
                if (this._Fetch_color(N.parent) == base._Color.RED &&
                    N.sibling != null &&
                    this._Fetch_color(N.sibling) == base._Color.BLACK &&
                    this._Fetch_color(N.sibling.left) == base._Color.BLACK &&
                    this._Fetch_color(N.sibling.right) == base._Color.BLACK) {
                    N.sibling.color = base._Color.RED;
                    N.parent.color = base._Color.BLACK;
                }
                else
                    this._Erase_case5(N);
            };
            _XTree.prototype._Erase_case5 = function (n) {
                if (n == n.parent.left &&
                    n.sibling != null &&
                    this._Fetch_color(n.sibling) == base._Color.BLACK &&
                    this._Fetch_color(n.sibling.left) == base._Color.RED &&
                    this._Fetch_color(n.sibling.right) == base._Color.BLACK) {
                    n.sibling.color = base._Color.RED;
                    n.sibling.left.color = base._Color.BLACK;
                    this._Rotate_right(n.sibling);
                }
                else if (n == n.parent.right &&
                    n.sibling != null &&
                    this._Fetch_color(n.sibling) == base._Color.BLACK &&
                    this._Fetch_color(n.sibling.left) == base._Color.BLACK &&
                    this._Fetch_color(n.sibling.right) == base._Color.RED) {
                    n.sibling.color = base._Color.RED;
                    n.sibling.right.color = base._Color.BLACK;
                    this._Rotate_left(n.sibling);
                }
                this._Erase_case6(n);
            };
            _XTree.prototype._Erase_case6 = function (n) {
                n.sibling.color = this._Fetch_color(n.parent);
                n.parent.color = base._Color.BLACK;
                if (n == n.parent.left) {
                    n.sibling.right.color = base._Color.BLACK;
                    this._Rotate_left(n.parent);
                }
                else {
                    n.sibling.left.color = base._Color.BLACK;
                    this._Rotate_right(n.parent);
                }
            };
            _XTree.prototype._Rotate_left = function (node) {
                var right = node.right;
                this._Replace_node(node, right);
                node.right = right.left;
                if (right.left != null)
                    right.left.parent = node;
                right.left = node;
                node.parent = right;
            };
            _XTree.prototype._Rotate_right = function (node) {
                var left = node.left;
                this._Replace_node(node, left);
                node.left = left.right;
                if (left.right != null)
                    left.right.parent = node;
                left.right = node;
                node.parent = left;
            };
            _XTree.prototype._Replace_node = function (oldNode, newNode) {
                if (oldNode.parent == null)
                    this.root_ = newNode;
                else {
                    if (oldNode == oldNode.parent.left)
                        oldNode.parent.left = newNode;
                    else
                        oldNode.parent.right = newNode;
                }
                if (newNode != null)
                    newNode.parent = oldNode.parent;
            };
            _XTree.prototype._Fetch_color = function (node) {
                if (node == null)
                    return base._Color.BLACK;
                else
                    return node.color;
            };
            return _XTree;
        }());
        base._XTree = _XTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _MapTree = (function (_super) {
            __extends(_MapTree, _super);
            function _MapTree(source, comp, it_comp) {
                var _this = _super.call(this, it_comp) || this;
                _this.source_ = source;
                _this.key_compare_ = comp;
                _this.key_eq_ = function (x, y) {
                    return !comp(x, y) && !comp(y, x);
                };
                _this.value_compare_ = function (x, y) {
                    return comp(x.first, y.first);
                };
                return _this;
            }
            _MapTree.prototype.get_by_key = function (key) {
                var ret = this.nearest_by_key(key);
                if (ret == null || !this.key_eq_(key, ret.value.first))
                    return null;
                else
                    return ret;
            };
            _MapTree.prototype.lower_bound = function (key) {
                var node = this.nearest_by_key(key);
                if (node == null)
                    return this.source().end();
                else if (this.key_comp()(node.value.first, key))
                    return node.value.next();
                else
                    return node.value;
            };
            _MapTree.prototype.equal_range = function (key) {
                return std.make_pair(this.lower_bound(key), this.upper_bound(key));
            };
            _MapTree.prototype.source = function () {
                return this.source_;
            };
            _MapTree.prototype.key_comp = function () {
                return this.key_compare_;
            };
            _MapTree.prototype.key_eq = function () {
                return this.key_eq_;
            };
            _MapTree.prototype.value_comp = function () {
                return this.value_compare_;
            };
            return _MapTree;
        }(base._XTree));
        base._MapTree = _MapTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _MultiMapTree = (function (_super) {
            __extends(_MultiMapTree, _super);
            function _MultiMapTree(source, comp) {
                return _super.call(this, source, comp, function (x, y) {
                    var ret = comp(x.first, y.first);
                    if (!ret && !comp(y.first, x.first))
                        return x.__get_m_iUID() < y.__get_m_iUID();
                    else
                        return ret;
                }) || this;
            }
            _MultiMapTree.prototype.insert = function (val) {
                val.__get_m_iUID();
                _super.prototype.insert.call(this, val);
            };
            _MultiMapTree.prototype._Nearest_by_key = function (key, equal_mover) {
                if (this.root_ == null)
                    return null;
                var ret = this.root_;
                var matched = null;
                while (true) {
                    var it = ret.value;
                    var my_node = null;
                    if (this.key_comp()(key, it.first))
                        my_node = ret.left;
                    else if (this.key_comp()(it.first, key))
                        my_node = ret.right;
                    else {
                        matched = ret;
                        my_node = equal_mover(ret);
                    }
                    if (my_node == null)
                        break;
                    else
                        ret = my_node;
                }
                return (matched != null) ? matched : ret;
            };
            _MultiMapTree.prototype.nearest_by_key = function (key) {
                return this._Nearest_by_key(key, function (node) {
                    return node.left;
                });
            };
            _MultiMapTree.prototype.upper_bound = function (key) {
                var node = this._Nearest_by_key(key, function (node) {
                    return node.right;
                });
                if (node == null)
                    return this.source().end();
                var it = node.value;
                if (this.key_comp()(key, it.first))
                    return it;
                else
                    return it.next();
            };
            return _MultiMapTree;
        }(base._MapTree));
        base._MultiMapTree = _MultiMapTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _SetTree = (function (_super) {
            __extends(_SetTree, _super);
            function _SetTree(set, comp, it_comp) {
                var _this = _super.call(this, it_comp) || this;
                _this.source_ = set;
                _this.key_comp_ = comp;
                _this.key_eq_ = function (x, y) {
                    return !comp(x, y) && !comp(y, x);
                };
                return _this;
            }
            _SetTree.prototype.get_by_key = function (val) {
                var ret = this.nearest_by_key(val);
                if (ret == null || !this.key_eq_(val, ret.value.value))
                    return null;
                else
                    return ret;
            };
            _SetTree.prototype.lower_bound = function (val) {
                var node = this.nearest_by_key(val);
                if (node == null)
                    return this.source_.end();
                else if (this.key_comp_(node.value.value, val))
                    return node.value.next();
                else
                    return node.value;
            };
            _SetTree.prototype.equal_range = function (val) {
                return std.make_pair(this.lower_bound(val), this.upper_bound(val));
            };
            _SetTree.prototype.source = function () {
                return this.source_;
            };
            _SetTree.prototype.key_comp = function () {
                return this.key_comp_;
            };
            _SetTree.prototype.key_eq = function () {
                return this.key_eq_;
            };
            _SetTree.prototype.value_comp = function () {
                return this.key_comp_;
            };
            return _SetTree;
        }(base._XTree));
        base._SetTree = _SetTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _MultiSetTree = (function (_super) {
            __extends(_MultiSetTree, _super);
            function _MultiSetTree(source, comp) {
                return _super.call(this, source, comp, function (x, y) {
                    var ret = comp(x.value, y.value);
                    if (!ret && !comp(y.value, x.value))
                        return x.__get_m_iUID() < y.__get_m_iUID();
                    else
                        return ret;
                }) || this;
            }
            _MultiSetTree.prototype.insert = function (val) {
                val.__get_m_iUID();
                _super.prototype.insert.call(this, val);
            };
            _MultiSetTree.prototype._Nearest_by_key = function (val, equal_mover) {
                if (this.root_ == null)
                    return null;
                var ret = this.root_;
                var matched = null;
                while (true) {
                    var it = ret.value;
                    var my_node = null;
                    if (this.key_comp()(val, it.value))
                        my_node = ret.left;
                    else if (this.key_comp()(it.value, val))
                        my_node = ret.right;
                    else {
                        matched = ret;
                        my_node = equal_mover(ret);
                    }
                    if (my_node == null)
                        break;
                    else
                        ret = my_node;
                }
                return (matched != null) ? matched : ret;
            };
            _MultiSetTree.prototype.nearest_by_key = function (val) {
                return this._Nearest_by_key(val, function (node) {
                    return node.left;
                });
            };
            _MultiSetTree.prototype.upper_bound = function (val) {
                var node = this._Nearest_by_key(val, function (node) {
                    return node.right;
                });
                if (node == null)
                    return this.source().end();
                var it = node.value;
                if (this.key_comp()(val, it.value))
                    return it;
                else
                    return it.next();
            };
            return _MultiSetTree;
        }(base._SetTree));
        base._MultiSetTree = _MultiSetTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _UniqueMapTree = (function (_super) {
            __extends(_UniqueMapTree, _super);
            function _UniqueMapTree(source, comp) {
                return _super.call(this, source, comp, function (x, y) {
                    return comp(x.first, y.first);
                }) || this;
            }
            _UniqueMapTree.prototype.nearest_by_key = function (key) {
                if (this.root_ == null)
                    return null;
                var ret = this.root_;
                while (true) {
                    var it = ret.value;
                    var my_node = null;
                    if (this.key_comp()(key, it.first))
                        my_node = ret.left;
                    else if (this.key_comp()(it.first, key))
                        my_node = ret.right;
                    else
                        return ret;
                    if (my_node == null)
                        break;
                    else
                        ret = my_node;
                }
                return ret;
            };
            _UniqueMapTree.prototype.upper_bound = function (key) {
                var node = this.nearest_by_key(key);
                if (node == null)
                    return this.source().end();
                var it = node.value;
                if (this.key_comp()(key, it.first))
                    return it;
                else
                    return it.next();
            };
            return _UniqueMapTree;
        }(base._MapTree));
        base._UniqueMapTree = _UniqueMapTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _UniqueSetTree = (function (_super) {
            __extends(_UniqueSetTree, _super);
            function _UniqueSetTree(source, comp) {
                return _super.call(this, source, comp, function (x, y) {
                    return comp(x.value, y.value);
                }) || this;
            }
            _UniqueSetTree.prototype.nearest_by_key = function (val) {
                if (this.root_ == null)
                    return null;
                var ret = this.root_;
                while (true) {
                    var it = ret.value;
                    var my_node = null;
                    if (this.key_comp()(val, it.value))
                        my_node = ret.left;
                    else if (this.key_comp()(it.value, val))
                        my_node = ret.right;
                    else
                        return ret;
                    if (my_node == null)
                        break;
                    else
                        ret = my_node;
                }
                return ret;
            };
            _UniqueSetTree.prototype.upper_bound = function (val) {
                var node = this.nearest_by_key(val);
                if (node == null)
                    return this.source().end();
                var it = node.value;
                if (this.key_comp()(val, it.value))
                    return it;
                else
                    return it.next();
            };
            return _UniqueSetTree;
        }(base._SetTree));
        base._UniqueSetTree = _UniqueSetTree;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    var base;
    (function (base) {
        var _XTreeNode = (function () {
            function _XTreeNode(value, color) {
                this.value = value;
                this.color = color;
                this.parent = null;
                this.left = null;
                this.right = null;
            }
            Object.defineProperty(_XTreeNode.prototype, "grand", {
                get: function () {
                    return this.parent.parent;
                },
                enumerable: true,
                configurable: true
            });
            Object.defineProperty(_XTreeNode.prototype, "sibling", {
                get: function () {
                    if (this == this.parent.left)
                        return this.parent.right;
                    else
                        return this.parent.left;
                },
                enumerable: true,
                configurable: true
            });
            Object.defineProperty(_XTreeNode.prototype, "uncle", {
                get: function () {
                    return this.parent.sibling;
                },
                enumerable: true,
                configurable: true
            });
            return _XTreeNode;
        }());
        base._XTreeNode = _XTreeNode;
    })(base = std.base || (std.base = {}));
})(std || (std = {}));
var std;
(function (std) {
    std.vector = std.Vector;
    std.deque = std.Deque;
    std.list = std.List;
    std.forward_list = std.ForwardList;
    std.stack = std.Stack;
    std.queue = std.Queue;
    std.priority_queue = std.PriorityQueue;
    std.set = std.TreeSet;
    std.multiset = std.TreeMultiSet;
    std.unordered_set = std.HashSet;
    std.unordered_multiset = std.HashMultiSet;
    std.map = std.TreeMap;
    std.multimap = std.TreeMultiMap;
    std.unordered_map = std.HashMap;
    std.unordered_multimap = std.HashMultiMap;
    std.exception = std.Exception;
    std.logic_error = std.LogicError;
    std.domain_error = std.DomainError;
    std.invalid_argument = std.InvalidArgument;
    std.length_error = std.LengthError;
    std.out_of_range = std.OutOfRange;
    std.runtime_error = std.RuntimeError;
    std.overflow_error = std.OverflowError;
    std.underflow_error = std.UnderflowError;
    std.range_error = std.RangeError;
    std.system_error = std.SystemError;
    std.error_category = std.ErrorCategory;
    std.error_condition = std.ErrorCondition;
    std.error_code = std.ErrorCode;
    std.mutex = std.Mutex;
    std.shared_mutex = std.SharedMutex;
    std.timed_mutex = std.TimedMutex;
    std.shared_timed_mutex = std.SharedTimedMutex;
    std.condition_variable = std.ConditionVariable;
})(std || (std = {}));
(function (std) {
    var experiments;
    (function (experiments) {
        experiments.semaphore = experiments.Semaphore;
        experiments.timed_semaphore = experiments.TimedSemaphore;
    })(experiments = std.experiments || (std.experiments = {}));
})(std || (std = {}));
try {
    module.exports = std;
}
catch (exception) { }
var std;
(function (std) {
    var JSArray;
    (function (JSArray) {
        JSArray.Iterator = std.Vector.Iterator;
        JSArray.ReverseIterator = std.Vector.ReverseIterator;
    })(JSArray = std.JSArray || (std.JSArray = {}));
})(std || (std = {}));
//# sourceMappingURL=tstl.js.map